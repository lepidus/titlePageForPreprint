<?php

class TitlePageRequirements
{
    public const CPDF_PATH = __DIR__ . "/../tools/cpdf";

    public function checkRequirements()
    {
        return $this->checkPdfManipulator() && $this->checkPdfGenerator();
    }

    private function checkPdfManipulator()
    {
        if (!is_executable(self::CPDF_PATH)) {
            $this->showMissingRequirementNotification('plugins.generic.titlePageForPreprint.requirements.pdfManipulatorMissing');
            return false;
        }
        return true;
    }

    private function checkPdfGenerator()
    {
        return true;
    }

    public function showMissingRequirementNotification($notificationMessage)
    {
        $currentUser = Application::get()->getRequest()->getUser();
        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification($currentUser->getId(), NOTIFICATION_TYPE_WARNING, array('contents' => __($notificationMessage)));
    }
}
