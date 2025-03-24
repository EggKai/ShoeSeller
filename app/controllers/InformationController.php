<?php
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../../core/Controller.php';

class InformationController extends Controller
{
    private const PATH = 'information';

    private function validateCoordinates($latitude, $longitude)
    {
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return false;
        }

        $latitude = floatval($latitude);
        $longitude = floatval($longitude);

        // Valid latitude range is -90 to 90
        // Valid longitude range is -180 to 180
        return $latitude >= -90 && $latitude <= 90 && $longitude >= -180 && $longitude <= 180;
    }

    public function aboutus()
    {
        $this->view(self::PATH . '/aboutus', ['options' => ['aboutus']]);
    }

    public function locations()
    {
        $locationModel = new Location();
        $locations = $locationModel->getAllLocations();
        $this->view(self::PATH . '/locations', [
            'options' => ['locations'],
            'locations' => $locations,
            'csrf_token' => Csrf::generateToken()
        ]);
    }

    public function doAddLocation()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
            (new UserController())->login();
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !filter_has_var(INPUT_POST, 'submit')) {
            (new HomeController())->index();
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request. Please try again.']);
            exit;
        }

        $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_SPECIAL_CHARS);
        $country = filter_var($_POST['country'], FILTER_SANITIZE_SPECIAL_CHARS);
        $zip_code = filter_var($_POST['zip_code'], FILTER_SANITIZE_SPECIAL_CHARS);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_SPECIAL_CHARS);
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        if (!$this->validateCoordinates($latitude, $longitude)) {
            echo json_encode(['success' => false, 'message' => 'Invalid latitude or longitude values.']);
            exit;
        }

        $locationModel = new Location();
        $id = $locationModel->createLocation(
            $name,
            $address,
            $country,
            $zip_code,
            $phone,
            $latitude,
            $longitude
        );

        if ($id) {
            echo json_encode(['success' => true, 'data' => [
                $id, $name, $address, $country, $zip_code, $phone, $latitude, $longitude
            ]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add location.']);
        }
        exit;
    }

    public function doUpdateLocation()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
            (new UserController())->login();
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !filter_has_var(INPUT_POST, 'submit')) {
            (new HomeController())->index();
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request. Please try again.']);
            exit;
        }

        $id = filter_var($_POST['id'], FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_SPECIAL_CHARS);
        $country = filter_var($_POST['country'], FILTER_SANITIZE_SPECIAL_CHARS);
        $zip_code = filter_var($_POST['zip_code'], FILTER_SANITIZE_SPECIAL_CHARS);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_SPECIAL_CHARS);
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        if (!$this->validateCoordinates($latitude, $longitude)) {
            echo json_encode(['success' => false, 'message' => 'Invalid latitude or longitude values.']);
            exit;
        }

        $locationModel = new Location();
        $updated = $locationModel->updateLocation(
            $id,
            $name,
            $address,
            $country,
            $zip_code,
            $phone,
            $latitude,
            $longitude
        );

        if ($updated) {
            echo json_encode(['success' => true, 'data' => [
                $id, $name, $address, $country, $zip_code, $phone, $latitude, $longitude
            ]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update location.']);
        }
        exit;
    }

    public function doRemoveLocation()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
            (new UserController())->login();
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !filter_has_var(INPUT_POST, 'submit')) {
            (new HomeController())->index();
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request. Please try again.']);
            exit;
        }

        $locationModel = new Location();
        $removed = $locationModel->deleteLocation($_POST['id']);

        if ($removed) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete location.']);
        }
        exit;
    }

    public function privacyPolicy()
    {
        $this->view(self::PATH . '/privacy-policy',['options'=>['privacy-policy']]);

    }

    public function termsAndConditions()
    {
        $this->view(self::PATH . '/terms-and-conditions',['options'=>['terms-and-conditions']]);
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
