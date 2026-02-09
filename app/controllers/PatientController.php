<?php
class PatientController {
    private $db;
    private $patient;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->patient = new Patient($this->db);
    }

    /**
     * POST /api/patients
     * [cite_start]Create and Link Patient with Unique Phone Validation [cite: 140-144]
     */
    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $user_email = $data['user_email'] ?? $data['email'] ?? null;
        $name = $data['name'] ?? null;
        $phone = $data['phone'] ?? null;

        // 1. Mandatory Field Check [cite: 72]
        if (empty($name) || empty($phone) || empty($user_email)) {
            Response::json(400, "Incomplete data. Name, Phone, and User Gmail are required.");
            exit();
        }

        // 2. Gmail Validator (Only @gmail.com)
        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL) || !str_ends_with($user_email, '@gmail.com')) {
            Response::json(400, "Invalid email format. Only @gmail.com addresses are accepted.");
            exit();
        }

        // 3. Phone Validator (0-9, exactly 10 digits)
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            Response::json(400, "Invalid phone number. Must be exactly 10 digits.");
            exit();
        }

        try {
            $data['user_email'] = $user_email;
            if ($this->patient->create($data)) {
                Response::json(201, "Patient created and linked to " . $user_email);
            }
        } catch (PDOException $e) {
            // Duplicate Phone Handling [cite: 162]
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                Response::json(400, "Duplicate Error: This phone number ($phone) is already registered.");
            } else if ($e->getCode() == 23000 || str_contains($e->getMessage(), '1452')) { 
                Response::json(404, "Error: No user found with the Gmail '" . $user_email . "'.");
            } else {
                Response::json(500, "System Error: " . $e->getMessage());
            }
        }
    }

    /**
     * PUT /api/patients/{id}
     * Robust Fix: Handles "No Change" scenario and "ID Not Found" separately
     */
    public function update($id = null) {
        // 1. ID Missing Validation
        if (empty($id)) {
            Response::json(400, "Update Error: Patient ID is required in the URL path.");
            exit();
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $user_email = $data['user_email'] ?? $data['email'] ?? null;
        $name = $data['name'] ?? null;
        $phone = $data['phone'] ?? null;

        // 2. Mandatory Data Validation
        if (empty($name) || empty($phone) || empty($user_email)) {
            Response::json(400, "Incomplete data for update.");
            exit();
        }

        $auth_email = AuthMiddleware::$currentUserEmail;

        // 3. First, check if the record exists and belongs to the user [cite: 147-149]
        $existingPatient = $this->patient->readOne($id, $auth_email);

        if (!$existingPatient) {
            Response::json(404, "Update failed: Patient ID $id not found or unauthorized access.");
            exit();
        }

        try {
            // 4. Perform update
            $stmt = $this->patient->update($id, $auth_email, $data);

            // Row count zero-va irunthaalum success message anuppalaam, 
            // yen-na ID exists-nu munnadiye verify pannittom.
            Response::json(200, "Patient updated successfully.");

        } catch (PDOException $e) {
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                Response::json(400, "Update Error: Phone number already exists for another patient.");
            } else {
                Response::json(500, "System Error: " . $e->getMessage());
            }
        }
    }

    /**
     * DELETE /api/patients/{id}
     * Fixed: Added ID missing error handle and RowCount check
     */
    public function destroy($id = null) {
        // 1. ID Missing Validation
        if (empty($id)) {
            Response::json(400, "Delete Error: Patient ID is required in the URL path.");
            exit();
        }

        $auth_email = AuthMiddleware::$currentUserEmail;

        try {
            $stmt = $this->patient->delete($id, $auth_email);

            /**
             * rowCount() check ensure pannum antha ID database-la delete aayirukku-nu.
             * [cite_start]Zero-va iruntha ID thappu illa unauthorized access [cite: 147-149].
             */
            if ($stmt->rowCount() > 0) {
                Response::json(200, "Patient deleted successfully.");
            } else {
                Response::json(404, "Delete failed: Patient ID $id not found or unauthorized access.");
            }
        } catch (PDOException $e) {
            Response::json(500, "System Error: " . $e->getMessage());
        }
    }

    /**
     * GET /api/patients
     */
    public function index() {
        $user_email = AuthMiddleware::$currentUserEmail; 
        if (!$user_email) {
            Response::json(401, "Unauthorized: User context missing.");
            exit();
        }
        $stmt = $this->patient->readByUser($user_email);
        Response::json(200, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * GET /api/patients/{id}
     */
    public function show($id) {
        $user_email = AuthMiddleware::$currentUserEmail;
        $row = $this->patient->readOne($id, $user_email);
        if ($row) {
            Response::json(200, $row);
        } else {
            Response::json(404, "Patient not found or unauthorized access.");
        }
    }
}