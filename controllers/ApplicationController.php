<?php
require_once __DIR__ . '/../models/ApplicationModel.php';

class ApplicationController {
    private ApplicationModel $model;
    private string $prefix;

    public function __construct(string $prefix = '') {
        $this->model = new ApplicationModel();
        $this->prefix = $prefix;
    }

    public function index(): void {
        $applications = $this->model->getAll();
        $prefix = $this->prefix;
        $title = "İş Başvuruları Listesi";
        $activePage = "applications";

        ob_start();
        include __DIR__ . '/../views/applications/index.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layout.php';
    }

    public function show(int $id): void {
        $app = $this->model->getById($id);
        if (!$app) {
            http_response_code(404);
            echo "Başvuru bulunamadı.";
            return;
        }

        $prefix = $this->prefix;
        $title = "Başvuru Detayı #" . $app['id'];
        $activePage = "applications";

        ob_start();
        include __DIR__ . '/../views/applications/show.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layout.php';
    }
}
