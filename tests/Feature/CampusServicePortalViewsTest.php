<?php

test('the portal homepage and nested pages are served from Blade views', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('/portal/login.html', false);

    $this->get('/portal/admin/users/index.html')
        ->assertOk()
        ->assertSee('Users — Campus Service Portal')
        ->assertSee('/campus-service-portal/assets/css/styles.css', false);
});

test('unknown portal pages return a not found response', function () {
    $this->get('/portal/not-a-page.html')->assertNotFound();
});
