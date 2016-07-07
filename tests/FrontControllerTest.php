<?php

use PHPUnit\Framework\TestCase;

class FrontControllerTest extends TestCase
{
    public function testInit()
    {
        //test the model layer
        $persistenceModel = $this->createMock(\Propeller\Models\PersistenceModel::class);
        $ormModel = $this->createMock(\Propeller\Models\OrmModel::class);
        $templateModel = $this->createMock(\Propeller\Models\TemplateModel::class);
        $urlModel = $this->createMock(\Propeller\Models\UrlModel::class);

        //test the controller layer
        $mainController = $this->createMock(\Propeller\Controllers\MainController::class);
        $mainController->method('manageInput')->willReturn(null);

        //test the view layer
        $mainView = $this->createMock(\Propeller\Views\MainView::class);
        $mainView->method('manageOutput')->willReturn(null);

        new \Propeller\Controllers\FrontController(
            $persistenceModel,
            $ormModel,
            $templateModel,
            $urlModel,
            $mainController,
            $mainView
        );
    }
}
