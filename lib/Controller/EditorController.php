<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Britton Couchman <brittondcouchman@gmail.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\WordCraftIntegration\Controller;

use OCA\WordCraftIntegration\AppInfo\Application;
use OCA\WordCraftIntegration\Service\Preferences;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\Files\File;
use OCP\Files\IRootFolder;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\Util;

/**
 * The editor page: Nextcloud's header with WordCraft in an iframe below it. The page script
 * (src/editor.ts) downloads the document over WebDAV, hands it to WordCraft and uploads saves.
 */
class EditorController extends Controller {
	public function __construct(
		IRequest $request,
		private IRootFolder $rootFolder,
		private IUserSession $userSession,
		private IURLGenerator $urlGenerator,
		private IInitialState $initialState,
		private Preferences $preferences,
	) {
		parent::__construct(Application::APP_ID, $request);
	}

	#[NoAdminRequired]
	#[NoCSRFRequired]
	#[FrontpageRoute(verb: 'GET', url: '/edit/{fileId}', requirements: ['fileId' => '\d+'])]
	public function edit(int $fileId): TemplateResponse {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return $this->notFound();
		}
		$userFolder = $this->rootFolder->getUserFolder($user->getUID());
		$file = $userFolder->getFirstNodeById($fileId);
		if (!($file instanceof File) || !$file->isReadable() || !in_array($file->getMimetype(), Application::MIME_TYPES, true)) {
			return $this->notFound();
		}

		$path = $userFolder->getRelativePath($file->getPath());
		if ($path === null) {
			return $this->notFound();
		}
		$dir = dirname($path);

		$this->initialState->provideInitialState('file', [
			'fileId' => $file->getId(),
			'name' => $file->getName(),
			'path' => $path,
			'canUpdate' => $file->isUpdateable(),
			'etag' => $file->getEtag(),
			'uid' => $user->getUID(),
		]);
		$this->initialState->provideInitialState('urls', [
			'frame' => $this->urlGenerator->linkToRoute(Application::APP_ID . '.frame.index', [
				'host' => 'parent',
				'author' => $user->getDisplayName(),
			]),
			'files' => $this->urlGenerator->linkToRoute('files.view.indexViewFileid', [
				'view' => 'files',
				'fileid' => $file->getId(),
				'dir' => $dir === '' ? '/' : $dir,
			]),
		]);
		$this->initialState->provideInitialState('openByDefault', $this->preferences->openByDefault($user->getUID()));

		Util::addStyle(Application::APP_ID, 'editor');
		Util::addScript(Application::APP_ID, Application::APP_ID . '-editor');

		$response = new TemplateResponse(Application::APP_ID, 'editor', ['name' => $file->getName()]);
		$csp = new ContentSecurityPolicy();
		$csp->addAllowedFrameDomain("'self'");
		$response->setContentSecurityPolicy($csp);
		return $response;
	}

	private function notFound(): TemplateResponse {
		return new TemplateResponse('core', '404', [], TemplateResponse::RENDER_AS_GUEST, Http::STATUS_NOT_FOUND);
	}
}
