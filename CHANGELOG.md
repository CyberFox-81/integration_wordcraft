<!--
  - SPDX-FileCopyrightText: 2026 Britton Couchman <brittondcouchman@gmail.com>
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Changelog

## 0.1.7 – 2026-10-27

- WordCraft 0.11.0 (commit 4b8b06f), which has its own embedding bridge now (storytold/wordcraft@caf5ea5): this app uses it instead of its own. The only patch left makes File › Save As › Browse… reach Nextcloud as Save As, so Nextcloud asks where to keep the file instead of the save going back over the opened one
- Save writes back to the file you opened in its own format, as WordCraft's desktop app does: an OpenDocument (.odt) or RTF file stays .odt or .rtf instead of becoming a new .docx. A Word 97–2003 .doc still goes to a new .docx
- Save As and Export ask for the name and folder in Nextcloud once WordCraft hands over the file; cancelling a Save As keeps the file to retry (Retry saving), and cancelling an export drops it

## 0.1.6 – 2026-10-21

- WordCraft 0.9.0 (commit f7d7c02): the latest WordCraft, with the embedding bridge rebased onto it

## 0.1.5 – 2026-10-16

- WordCraft 0.7.0 (commit eae2421): the latest WordCraft, with the embedding bridge rebased onto it

## 0.1.4 – 2026-10-14

- WordCraft 0.7.0 (commit 3655330): the latest WordCraft, with the embedding bridge rebased onto it

## 0.1.3 – 2026-10-12

- WordCraft 0.6.1 (commit 159b84f): the latest WordCraft, with the embedding bridge rebased onto it
- Opening a document from Files goes straight to it, without WordCraft's new File › Home start screen first

## 0.1.2 – 2026-10-11

- WordCraft 0.5.0 (commit 850fa05): the latest WordCraft, with the embedding bridge rebased onto it

## 0.1.1 – 2026-10-11

- WordCraft 0.5.0 (commit 1d4f741): the latest WordCraft, with the embedding bridge rebased onto it

## 0.1.0 – 2026-10-10

First release.

- App ID `integration_wordcraft`: "Edit in WordCraft" in the Files action menu for Word (.docx, .docm, .dotx, .dotm, .doc), OpenDocument text and RTF files (Nextcloud 33–35)
- Editor page with WordCraft 0.4.0 (commit 81bf918) in an iframe
- Save writes back over WebDAV with conflict detection; Save As and Export ask for a name and folder in Nextcloud
- Read-only files, and formats WordCraft saves as .docx, go to a new file
- Optional "open documents in WordCraft when clicked", per user, with an admin default
