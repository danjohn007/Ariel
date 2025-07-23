<?php
/**
 * Admin Controller
 */
class AdminController extends Controller
{
    public function dashboard()
    {
        $this->requireRole('admin');
        
        $user = $this->getCurrentUser();
        
        $this->view->set([
            'title' => 'Panel de Administración',
            'user' => $user
        ]);
        
        $this->view->render('admin/dashboard');
    }
}