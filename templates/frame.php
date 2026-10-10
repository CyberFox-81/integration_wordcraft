<?php
/**
 * SPDX-FileCopyrightText: 2026 Britton Couchman <brittondcouchman@gmail.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
/** @var array $_ */
?>
<!DOCTYPE html>
<html lang="<?php p($_['language']); ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="robots" content="noindex, nofollow">
	<title>WordCraft</title>
	<style>
		html, body { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; background: #e6e6e6; }
		#wordcraft_canvas { position: absolute; inset: 0; width: 100%; height: 100%; display: block; }
		#wordcraft_loading { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; }
		@media (prefers-color-scheme: dark) { html, body { background: #1e1e20; } }
	</style>
</head>
<body>
	<canvas id="wordcraft_canvas"></canvas>
	<div id="wordcraft_loading"></div>
	<?php emit_script_tag($_['bootUrl'], '', 'module'); ?>
</body>
</html>
