<?php
namespace App\Http\Middleware;

use Closure;

class SecureHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // HSTS: Paksa browser selalu pakai HTTPS selama 1 tahun
        $response->headers->set('Strict-Transport-Security', 
            'max-age=31536000; includeSubDomains; preload');
        
        // Cegah clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Cegah MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Cegah XSS
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Content Security Policy (sangat penting!)
        $response->headers->set('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
            "img-src 'self' data: blob: https:; " .
            "font-src 'self' https://fonts.gstatic.com; " .
            "connect-src 'self'");
        
        // Jangan bocorkan versi server
        $response->headers->set('X-Powered-By', '');
        $response->headers->set('Server', '');
        
        return $response;
    }
}