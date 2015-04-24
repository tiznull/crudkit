<?php

namespace CrudKit\Controllers;

use CrudKit\CrudKitApp;
use CrudKit\Pages\BasePage;
use CrudKit\Util\TwigUtil;
use CrudKit\Util\UrlHelper;
use Exception;

class BaseController {

    /**
     * @var UrlHelper
     */
    protected $url = null;

    /**
     * @var CrudKitApp
     */
    protected $app = null;

    /**
     * @var TwigUtil
     */
    protected $twig = null;

    /**
     * @var BasePage
     */
    protected $page = null;

    /**
     * @param $app CrudKitApp
     */
    public function __construct ($app) {
        $this->app = $app;
        $this->url = new UrlHelper();
        $this->twig = new TwigUtil();
    }

    public function handle () {
        $action = $this->url->get("action", "default");
        $result = null;
        if(method_exists($this, "handle_".$action)) {
            $result = call_user_func(array($this, "handle_". $action));
        }
        else {
            throw new Exception ("Unknown action");
        }
        $output = "";

        if(is_string($result)) {
            $newResult = array(
                'type' => 'transclude',
                'content' => $result
            );

            $result = $newResult;
        }

        switch($result['type']) {
            case "template":
                $output = $this->twig->renderTemplateToString($result['template'], $result['data']);
                break;
            case "json":
                $this->app->setJsonResponse(true);
                $output = json_encode($result['data']);
                break;
            case "transclude":
                $pageMap = [];
                /** @var BasePage $pageItem */
                foreach($this->app->getPages() as $pageItem) {
                    $pageMap []= array(
                        'id' => $pageItem->getId(),
                        'name' => $pageItem->getName()
                    );
                }
                $data = array(
                    'staticRoot' => $this->app->getStaticRoot(),
                    'pageMap' => $pageMap
                );
                if($this->page !== null) {
                    $data['page'] = $this->page;
                }
                $data['page_content'] = $result['content'];

                $output = $this->twig->renderTemplateToString("main_page.twig", $data);
                break;
            default:
                throw new Exception ("Unknown result type");
        }

        return $output;

    }
}