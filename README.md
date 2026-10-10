<!--
  - SPDX-FileCopyrightText: 2026 Britton Couchman <brittondcouchman@gmail.com>
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# WordCraft integration for Nextcloud

Write and edit the documents in your Nextcloud with [WordCraft](https://github.com/storytold/wordcraft), the open-source, Rust-native word processor, without leaving the browser.

- **Edit in WordCraft** appears in the Files action menu of every Word (.docx, .docm, .dotx, .dotm, .doc), OpenDocument text (.odt) and RTF file you can read.
- **Save** (Ctrl/⌘+S) writes the document back to Nextcloud, which keeps the previous state as a file version.
- **Save As** and **Export** (PDF, ODT, RTF, plain text, PNG…) ask for a name and folder in your Nextcloud.
- Read-only files (for example a share without edit rights) open normally; saving offers a copy.
- WordCraft writes Word documents back in place. A .doc, .odt or .rtf file is saved as a new .docx next to it (the original stays as it is); use **Save As** with an `.odt` or `.rtf` name to keep that format.
- If someone else changes the file while you have it open, saving asks whether to replace their version or save a copy.
- Optionally, clicking a document in Files opens it in WordCraft: tick **Open documents here from Files** in the editor's top bar.

WordCraft runs entirely in your browser as WebAssembly. Documents go only between your browser and your Nextcloud; there is no document server to run.

## Requirements

- Nextcloud 33, 34 or 35
- A browser with WebGPU or WebGL2 (any current Chrome, Edge, Firefox or Safari)
- The web server must serve `.wasm` files as `application/wasm`. Nextcloud's own `.htaccess` does this on Apache; the [recommended nginx configuration](https://docs.nextcloud.com/server/latest/admin_manual/installation/nginx.html) does too.
- Recommended: gzip or Brotli for `application/wasm`. WordCraft is fetched once and then cached.

## Install

From the Nextcloud App Store: **Apps ▸ Office & text ▸ WordCraft integration ▸ Download and enable**, or `sudo -u www-data php occ app:install integration_wordcraft`.

Manually:

1. Download `integration_wordcraft-<version>.tar.gz` from the [releases](https://github.com/CyberFox-81/integration_wordcraft/releases) and extract it into your Nextcloud's `custom_apps/` (or `apps/`) folder, so you have `custom_apps/integration_wordcraft/appinfo/info.xml`.
2. Make it readable by the web server user, e.g. `chown -R www-data:www-data custom_apps/integration_wordcraft`.
3. Enable it: `sudo -u www-data php occ app:enable integration_wordcraft`. In Docker: `docker exec -u www-data nextcloud php occ app:enable integration_wordcraft`.

To make WordCraft the click-to-open default for every user who hasn't chosen otherwise:

```sh
sudo -u www-data php occ config:app:set integration_wordcraft open_by_default --value=true --type=boolean
```

## How it works

```
Files ── "Edit in WordCraft" ──▶ /apps/integration_wordcraft/edit/{fileId}   (src/editor.ts)
                                  │  WebDAV GET/PUT with the user's session
                                  ▼
                                <iframe> /apps/integration_wordcraft/frame?host=parent   (templates/frame.php)
                                  │  WordCraft (web/*.wasm) — postMessage bridge
```

The editor page downloads the document over WebDAV and passes it to WordCraft with `postMessage`. When you save, WordCraft passes the bytes back and the page uploads them with `If-Match`, so a newer version on the server is never overwritten without asking. Save As and Export ask the page for a location, which it picks with Nextcloud's own folder picker. The frame page is the only one whose Content Security Policy allows compiling WebAssembly.

WordCraft's browser build saves by downloading a file. `patches/wordcraft-nextcloud.patch` adds an opt-in embedding bridge (`?host=parent`) to its web shell only, using the save, file-dialog and unsaved-changes hooks WordCraft already has; the protocol is in [web/README.md](web/README.md). It is meant to be proposed upstream, so a future official WordCraft build can be used as is.

## Build from source

```sh
make js      # npm ci && vite build → js/
make wasm    # clone WordCraft, apply the patch, build → web/   (needs Rust, see below)
make dist    # → build/dist/integration_wordcraft-<version>.tar.gz
```

`make wasm` runs `scripts/build-wordcraft-web.sh`, which needs the `wasm32-unknown-unknown` Rust target, `wasm-bindgen-cli` at the version in WordCraft's `Cargo.lock` (0.2.129 at the pinned commit), and optionally `wasm-opt` from binaryen. Set `WORDCRAFT_REF` to build another WordCraft commit, `FAST=1` for quicker development builds, and `CRAFT_FONTS_DIR` to a clone of [storytold/craft-fonts](https://github.com/storytold/craft-fonts) to embed the shared Japanese fonts.

For development, `npm run watch` rebuilds the scripts as you edit; symlink or copy the repository into `custom_apps/integration_wordcraft` of a test server.

## Releasing to the App Store

One-time setup (see the [App Store developer guide](https://nextcloudappstore.readthedocs.io/en/latest/developer.html)):

1. Create the app's key and certificate request, and keep the key private and backed up:
   ```sh
   mkdir -p ~/.nextcloud/certificates && cd ~/.nextcloud/certificates
   openssl req -nodes -newkey rsa:4096 -keyout integration_wordcraft.key -out integration_wordcraft.csr -subj "/CN=integration_wordcraft"
   ```
2. Submit `integration_wordcraft.csr` as `integration_wordcraft/integration_wordcraft.csr` in a pull request to [nextcloud/app-certificate-requests](https://github.com/nextcloud/app-certificate-requests). Save the signed certificate they reply with as `~/.nextcloud/certificates/integration_wordcraft.crt`.
3. Register the app at [apps.nextcloud.com/developer/apps/new](https://apps.nextcloud.com/developer/apps/new) with the certificate and this signature:
   ```sh
   echo -n "integration_wordcraft" | openssl dgst -sha512 -sign ~/.nextcloud/certificates/integration_wordcraft.key | openssl base64
   ```

Each release:

```sh
make js && make wasm
scripts/sign-release.sh --key ~/.nextcloud/certificates/integration_wordcraft.key \
  --cert ~/.nextcloud/certificates/integration_wordcraft.crt --docker nextcloud
```

The script code-signs the app with `occ integrity:sign-app` (borrowed from a running Nextcloud container, or `--occ` for a local install; `--archive` signs a prebuilt archive), packs `integration_wordcraft-<version>.tar.gz` and prints the App Store signature. Attach the archive to a GitHub release, then submit its download URL and the signature at [apps.nextcloud.com/developer/apps/releases/new](https://apps.nextcloud.com/developer/apps/releases/new).

## Limitations

- WordCraft is young; see its [roadmap](https://github.com/storytold/wordcraft/blob/main/ROADMAP.md) for what's still missing compared with Word.
- One person edits at a time. There is no live co-editing; concurrent saves are caught and you choose what happens.
- Files locked by another app (for example while open in Collabora or ONLYOFFICE) can't be saved until the lock is released; save a copy instead.

## License

This app is licensed under the AGPL-3.0-or-later. WordCraft, bundled in `web/`, is licensed under MIT OR Apache-2.0; its bundled fonts and assets carry their own open licenses (see `web/WORDCRAFT-ATTRIBUTION.md`). "WordCraft" refers to the upstream project by the ArtCraft team; this integration is not affiliated with them.
