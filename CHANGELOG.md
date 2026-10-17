<!--
  - SPDX-FileCopyrightText: 2026 Britton Couchman <brittondcouchman@gmail.com>
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Changelog

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
