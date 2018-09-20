<?php

namespace app\http\middleware;

class Auth
{
    public function handle($request, \Closure $next)
    {
    	if (session("?admin_id")) {
    		return $next($request);
    	}else{
    		return view("/index/login");
    	}
    	
    }
}
