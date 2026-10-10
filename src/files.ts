/**
 * SPDX-FileCopyrightText: 2026 Britton Couchman <brittondcouchman@gmail.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import type { IFileAction, INode } from '@nextcloud/files'

import { getCurrentUser } from '@nextcloud/auth'
import { DefaultType, FileType, Permission, registerFileAction } from '@nextcloud/files'
import { loadState } from '@nextcloud/initial-state'
import { t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import { isPublicShare } from '@nextcloud/sharing/public'
import { wordCraftIcon } from './icon.ts'

const openByDefault = loadState<boolean>('integration_wordcraft', 'openByDefault', false)

/** The document types WordCraft opens; keep in sync with Application::MIME_TYPES. */
const MIME_TYPES = new Set([
	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	'application/vnd.ms-word.document.macroEnabled.12',
	'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
	'application/vnd.ms-word.template.macroEnabled.12',
	'application/msword',
	'application/vnd.oasis.opendocument.text',
	'text/rtf',
	'application/rtf',
])

/**
 * A document WordCraft opens, in the user's own file tree (not the trash bin, versions or a
 * public share).
 *
 * @param node - The file to check
 */
function isEditableDocument(node: INode | undefined): node is INode {
	return node !== undefined
		&& node.type === FileType.File
		&& MIME_TYPES.has(node.mime)
		&& node.fileid !== undefined
		&& (node.permissions & Permission.READ) !== 0
		&& node.root?.startsWith('/files/') === true
}

const editAction: IFileAction = {
	id: 'wordcraft-edit',
	displayName: () => t('integration_wordcraft', 'Edit in WordCraft'),
	iconSvgInline: () => wordCraftIcon,

	enabled({ nodes }) {
		return getCurrentUser() !== null && !isPublicShare() && nodes.length === 1 && isEditableDocument(nodes[0])
	},

	async exec({ nodes }) {
		const fileId = nodes[0].fileid
		if (fileId === undefined) {
			return false
		}
		window.location.assign(generateUrl('/apps/integration_wordcraft/edit/{fileId}', { fileId }))
		return null
	},

	// Ahead of the Viewer's and office apps' default actions when WordCraft is the user's default.
	...(openByDefault ? { default: DefaultType.DEFAULT, order: -1 } : { order: 20 }),
}

registerFileAction(editAction)
