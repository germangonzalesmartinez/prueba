<?php

/**
 * @defgroup pages_articles Articles archive page
 */

/**
 * @file pages/articles/index.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup pages_articles
 * @brief Handle requests for articles archive view.
 *
 */

switch ($op) {
    case 'index':
    case 'view':
    case 'download':
        define('HANDLER_CLASS', 'APP\pages\articles\ArticlesHandler');
        break;
    case 'category':
    case 'fullSize':
    case 'thumbnail':
        define('HANDLER_CLASS', 'PKP\pages\publication\PKPCategoryHandler');
        break;
    case 'section':
        define('HANDLER_CLASS', 'APP\pages\articles\SectionsHandler');
        break;
}
