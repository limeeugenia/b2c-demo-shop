<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Installer\Business\Icecat\Importer\Cms;

use Orm\Zed\Cms\Persistence\SpyCmsPageQuery;
use Symfony\Component\Console\Output\OutputInterface;

class CmsPageImporter extends CmsBlockImporter
{

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'CMS Page';
    }

    /**
     * @return bool
     */
    public function isImported()
    {
        $query = SpyCmsPageQuery::create();
        return $query->count() > 0;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    protected function importOne(array $data)
    {
        $page = $this->format($data);
        $templateTransfer = $this->findOrCreateTemplate($page[self::TEMPLATE]);
        $pageTransfer = null;

        foreach ($this->localeFacade->getLocaleCollection() as $locale => $localeTransfer) {
            $url = $page[self::LOCALES][$locale][self::URL];
            if ($this->urlFacade->hasUrl($url)) {
                return;
            }

            if ($pageTransfer === null) {
                $pageTransfer = $this->createPage($templateTransfer);
            }

            $placeholders = $page[self::LOCALES][$locale][self::PLACEHOLDERS];

            $this->createPageUrl($pageTransfer, $url, $localeTransfer);
            $this->createPlaceholder($placeholders, $pageTransfer, $localeTransfer);
        }
    }

}