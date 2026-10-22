<?php
require_once __DIR__ . '/../models/JobPostingModel.php';
require_once __DIR__ . '/../models/ApplicationModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class JobPostingController {
    private JobPostingModel $model;
    private ApplicationModel $appModel;
    private UserModel $userModel;
    private string $prefix;

    public function __construct(string $prefix = '') {
        $this->model = new JobPostingModel();
        $this->appModel = new ApplicationModel();
        $this->userModel = new UserModel();
        $this->prefix = $prefix;
    }

    public function index(): void {
        $jobPostings = $this->model->getAll();
        $prefix = $this->prefix;
        $title = "İş ve Staj İlanları";
        $activePage = "job_postings";

        ob_start();
        include __DIR__ . '/../views/job_postings/index.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layout.php';
    }

    public function show(int $id, string $message = ''): void {
        $job = $this->model->getById($id);
        if (!$job) {
            http_response_code(404);
            echo "İş ilanı bulunamadı.";
            return;
        }

        $applications = $this->appModel->getByJobPostingId($id);
        $prefix = $this->prefix;
        $title = $job['title'] . " - " . $job['company'];
        $activePage = "job_postings";

        ob_start();
        include __DIR__ . '/../views/job_postings/show.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layout.php';
    }

    public function create(): void {
        $users = $this->userModel->getAllUsers();
        $prefix = $this->prefix;
        $title = "Yeni İş İlanı Ekle";
        $activePage = "job_postings";

        ob_start();
        include __DIR__ . '/../views/job_postings/create.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layout.php';
    }

    public function store(array $data): void {
        $this->model->create($data);
        header('Location: ' . $this->prefix . '/job-postings');
        exit;
    }

    public function apply(int $jobId, array $data): void {
        $data['job_posting_id'] = $jobId;
        $this->appModel->create($data);
        header('Location: ' . $this->prefix . '/job-postings/' . $jobId . '?applied=1');
        exit;
    }
}
