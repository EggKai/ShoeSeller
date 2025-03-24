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
        $this->view(InformationController::PATH . '/privacy-policy',['options'=>['privacy-policy']]);

    }
    public function termsAndConditions()
    {
        $this->view(InformationController::PATH . '/terms-and-conditions',['options'=>['terms-and-conditions']]);
    }
    public function cookiePolicy()
    {
        $this->view(InformationController::PATH . '/cookie-policy',['options'=>['cookie-policy']]);

    }
    public function accessibility()
    {
        $this->view(InformationController::PATH . '/accessibility',['options'=>['accessibility']]);

    }
    public function cookiepreference()
    {
        $this->view(InformationController::PATH . '/cookie-preferences',['options'=>['cookie-preferences']]);

    }
    public function Regulatoryframework()
    {
        $this->view(InformationController::PATH . '/Regulatoryframework',['options'=>['regulatory-framework']]);

    }
}