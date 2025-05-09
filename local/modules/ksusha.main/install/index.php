<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\SystemException;

Loc::getMessage(__FILE__);

class ksusha_main extends CModule
{
    public $MODULE_ID = 'ksusha.main';
    public $MODULE_SORT = 500;
    public $MODULE_VERSION;
    public $MODULE_DESCRIPTION;
    public $MODULE_VERSION_DATE;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_DESCRIPTION = Loc::getMessage('KSUSHA_MAIN_INSTALL_MODULE_DESCRIPTION');
        $this->MODULE_NAME = Loc::getMessage('KSUSHA_MAIN_INSTALL_MODULE_NAME');
        $this->PARTNER_NAME = Loc::getMessage('KSUSHA_MAIN_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('KSUSHA_MAIN_PARTNER_URI');
    }

    /**
     * @throws SystemException
     */
    public function DoInstall(): void
    {
        if ($this->isVersionD7()) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallEvents();
        } else {
            throw new SystemException(Loc::getMessage('KSUSHA_MAIN_INSTALL_ERROR_VERSION'));
        }
    }
    public function DoUninstall(): void
    {
        $this->UnInstallEvents();

        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler(
            'main',
            'OnAfterUserLogin',
            $this->MODULE_ID,
            '\\Ksusha\\Main\\Events\\Main',
            'onAfterUserLoginHandler',
        );

        $eventManager->registerEventHandler(
            'main',
            'OnAfterUserLogout',
            $this->MODULE_ID,
            '\\Ksusha\\Main\\Events\\Main',
            'OnAfterUserLogoutHandler',
        );
    }

    public function UnInstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'main',
            'OnAfterUserLogin',
            $this->MODULE_ID,
            '\\Ksusha\\Main\\Events\\Main',
            'onAfterUserLoginHandler',
        );

        $eventManager->unRegisterEventHandler(
            'main',
            'OnAfterUserLogout',
            $this->MODULE_ID,
            '\\Ksusha\\Main\\Events\\Main',
            'OnAfterUserLogoutHandler',
        );
    }

    public function isVersionD7(): bool
    {
        return CheckVersion(ModuleManager::getVersion('main'), '20.00.00');
    }
}
