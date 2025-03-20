<?php
require_once __DIR__ . '/../../core/Controller.php';

class InformationController extends Controller
{
    private const PATH = 'information';
    public function aboutus()
    {
        $this->view(InformationController::PATH . '/aboutus', ['options'=>['aboutus']]);
    }
    public function locations()
    {
        $this->view(InformationController::PATH . '/locations');
    }
    public function privacyPolicy()
    {
        $this->view(InformationController::PATH . '/privacy-policy');
    }
    public function termsAndConditions()
    {
        $this->view(InformationController::PATH . '/terms-and-conditions');
    }
}