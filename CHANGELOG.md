<!--
  - SPDX-FileCopyrightText: 2026 Britton Couchman <brittondcouchman@gmail.com>
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Changelog

## 0.1.0 – 2026-10-10

First release.

- App ID `integration_wordcraft`: "Edit in WordCraft" in the Files action menu for Word (.docx, .docm, .dotx, .dotm, .doc), OpenDocument text and RTF files (Nextcloud 33–35)
- Editor page with WordCraft 0.4.0 (commit 81bf918) in an iframe
- Save writes back over WebDAV with conflict detection; Save As and Export ask for a name and folder in Nextcloud
- Read-only files, and formats WordCraft saves as .docx, go to a new file
- Optional "open documents in WordCraft when clicked", per user, with an admin default
