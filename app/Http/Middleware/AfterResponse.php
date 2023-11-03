<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Log;

class AfterResponse
{
    public $log;

    public function __construct(Log $log)
    {
    	$this->log = $log;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $timeStart = microtime(true);
        $response = $next($request);
        
        if (config('app.env') != 'local') {
            $resp = $response->getData();
        
            $data = [
                "service" => "sdm-service",
                "response_code" => $resp->code,
                "response_message" => $resp->message,
                "request_path" => $request->path(),
                "request_method" => $request->method(),
                "request_url" => $request->fullUrl(),
                "request_param" => json_encode($request->all()),
                "request_ip" => $request->ip(),
                "exec_time" => floor((microtime(true) - $timeStart) * 1000),
                "memory_usage" => round(memory_get_peak_usage(true) / 1024 / 1024, 1),
            ];
            if (property_exists($resp, 'errors')) $data['response_error'] = json_encode($resp->errors);
            
            $log = $this->log->createLog($data);
        }

        return $response;
    }
}
