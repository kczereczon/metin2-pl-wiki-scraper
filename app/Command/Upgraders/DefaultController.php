<?php

namespace App\Command\Upgraders;

use Minicli\Command\CommandController;
use Minicli\Output\Filter\ColorOutputFilter;
use Minicli\Output\Helper\TableHelper;
use Symfony\Component\BrowserKit\HttpBrowser;

class DefaultController extends CommandController
{
    public function handle(): void
    {
        $httpClient = new HttpBrowser();
        $request = $httpClient->request('GET', 'https://pl-wiki.metin2.gameforge.com/index.php/Ulepszacze');
        $nameNodes = $request->evaluate('//*[@id="mw-content-text"]/div/table/tbody/tr/td[2]/a');
        $imageNodes = $request->evaluate('//*[@id="mw-content-text"]/div/table/tbody/tr/td[1]/a/img');
        $descriptionNodes = $request->evaluate('//*[@id="mw-content-text"]/div/table/tbody/tr/td[4]');
        $this->getPrinter()->display('Testing Tables');

        $table = new TableHelper();
        $table->addHeader(['Name', 'Description', 'Image']);

        $names = [];
        $descriptions = [];
        $images = [];


        foreach ($nameNodes as $nameNode) {
            $names[] = $nameNode->textContent;
        }

        foreach ($imageNodes as $imageNode) {
            $images[] = $imageNode->attributes->getNamedItem('src')->textContent;
        }

        foreach ($descriptionNodes as $descriptionNode) {
            $descriptions[] = $descriptionNode->textContent;
        }

        foreach ($names as $key => $name) {
                    $table->addRow([(string)$name, (string)$descriptions[$key], 'https://pl-wiki.metin2.gameforge.com/' . (string)$images[$key]]);
        }

        $this->getPrinter()->newline();
        $this->getPrinter()->rawOutput($table->getFormattedTable(new ColorOutputFilter()));
        $this->getPrinter()->newline();

    }
}