<?php

require_once __DIR__ . '/../models/AnnouncementModel.php';

class AnnouncementController {
    private AnnouncementModel $model;
    private string $prefix;

    public function __construct(string $prefix = '') {
        $this->model = new AnnouncementModel();
        $this->prefix = $prefix;
    }

    private function render(string $view, array $data = []) {
        extract($data);
        $prefix = $this->prefix;
        $activePage = $activePage ?? 'announcements';
        $title = $title ?? 'Duyurular';
        
        ob_start();
        require __DIR__ . '/../views/' . $view . '.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/../views/layout.php';
    }

    public function index() {
        $items = $this->model->getAll();
        $this->render('announcements/index', ['items' => $items, 'title' => 'Duyurular']);
    }

    public function create() {
        $this->render('announcements/create', ['title' => 'Yeni Duyuru']);
    }

    public function store(array $data) {
        $this->model->create($data);
        header('Location: ' . $this->prefix . '/announcements');
        exit;
    }

    public function show(int $id) {
        $item = $this->model->getById($id);
        if (!$item) { echo "Bulunamadı."; exit; }
        $this->render('announcements/show', ['item' => $item, 'title' => 'Duyuru Detayı']);
    }

    public function edit(int $id) {
        $item = $this->model->getById($id);
        if (!$item) { echo "Bulunamadı."; exit; }
        $this->render('announcements/edit', ['item' => $item, 'title' => 'Duyuru Düzenle']);
    }

    public function update(int $id, array $data) {
        $this->model->update($id, $data, true);
        header('Location: ' . $this->prefix . '/announcements/' . $id);
        exit;
    }

    public function destroy(int $id) {
        $this->model->delete($id);
        header('Location: ' . $this->prefix . '/announcements');
        exit;
    }
}
