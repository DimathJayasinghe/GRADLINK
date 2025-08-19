<?php
class Logout extends Controller
{
    public function __construct()
    {
        // No initialization needed
    }

    // Default method - handles logout
    public function index()
    {
        // Use SessionManager to properly destroy session
        SessionManager::destroySession();
        
        // Set success flash message
        SessionManager::setFlash('success', 'You have been logged out successfully');
        
        $this->redirect("/auth");
    }
}
?>