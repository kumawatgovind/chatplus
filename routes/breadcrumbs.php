<?php
use Illuminate\Support\Str;

Breadcrumbs::for('home', function ($trail) {
    $trail->push('Dashboard', route('admin.dashboard'), ['icon' => 'fa fa-dashboard']);
});

Breadcrumbs::for('website', function ($trail) {
    $trail->push('Home', route('frontend.home'), ['icon' => 'fa fa-dashboard']);
});

// Home > About
Breadcrumbs::for('common', function ($trail, $append) {
    $trail->parent('home');
    if(!empty($append['append'])){
        foreach($append['append'] as $crum){
            $label = Str::title(str_replace("-", " ", Str::kebab(ucfirst($crum['label']))));
            $trail->push($label, isset($crum['route'])? route($crum['route']) : (isset($crum['url'])? $crum['url'] : ''));
        }
    }
});


// Home > About
Breadcrumbs::for('front', function ($trail, $append) {
    $trail->parent('website');
    if(!empty($append['append'])){
        foreach($append['append'] as $crum){
            $label = Str::title(str_replace("-", " ", Str::kebab(ucfirst($crum['label']))));
            $trail->push($label, isset($crum['route'])? route($crum['route']) : (isset($crum['url'])? $crum['url'] : ''));
        }
    }
});