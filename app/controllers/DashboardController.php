<?php
/**
 * Dashboard Controller
 */
class DashboardController extends Controller
{
    public function index()
    {
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        $role = $user['role'];
        
        // Redirect to role-specific dashboard
        switch ($role) {
            case 'admin':
                $this->redirect('/admin/dashboard');
                break;
            case 'coordinator':
                $this->redirect('/services/manage');
                break;
            case 'mechanic':
                $this->redirect('/mechanic/dashboard');
                break;
            case 'client':
                $this->redirect('/client/dashboard');
                break;
            default:
                $this->showDashboard($user);
        }
    }
    
    private function showDashboard($user)
    {
        $this->view->set([
            'title' => 'Dashboard',
            'user' => $user
        ]);
        
        $this->view->render('dashboard/index');
    }
}