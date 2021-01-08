<?php
/**
 * @defgroup plugins_generic_pluginTemplate
 */
/**
 * @file plugins/generic/TitlePageForPreprint/index.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_pluginTemplate
 * @brief Wrapper for the Title Page plugin.
 *
 */
require_once('TitlePagePlugin.inc.php');
return new TitlePagePlugin();
