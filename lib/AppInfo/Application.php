<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Britton Couchman <brittondcouchman@gmail.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\WordCraftIntegration\AppInfo;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\WordCraftIntegration\Listener\LoadFilesScripts;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
	public const APP_ID = 'integration_wordcraft';

	/** User and app config key: open documents in WordCraft when they are clicked in Files. */
	public const OPEN_BY_DEFAULT = 'open_by_default';

	/**
	 * The document types WordCraft opens: Word (.docx, .docm, .dotx, .dotm, .doc), OpenDocument
	 * text (.odt) and RTF. Keep in sync with src/files.ts.
	 */
	public const MIME_TYPES = [
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.ms-word.document.macroEnabled.12',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
		'application/vnd.ms-word.template.macroEnabled.12',
		'application/msword',
		'application/vnd.oasis.opendocument.text',
		'text/rtf',
		'application/rtf',
	];

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(LoadAdditionalScriptsEvent::class, LoadFilesScripts::class);
	}

	public function boot(IBootContext $context): void {
	}
}
