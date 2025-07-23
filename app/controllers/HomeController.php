<?php
/**
 * Home Controller
 */
class HomeController extends Controller
{
    public function index()
    {
        $this->view->set([
            'title' => 'Inicio',
            'metaDescription' => 'Servicio profesional de mecánicos a domicilio. Solicita tu servicio ahora.'
        ]);
        
        $this->view->render('home/index');
    }
}