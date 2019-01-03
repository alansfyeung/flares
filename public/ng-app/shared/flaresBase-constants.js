(function () {
    // ==================================
    //   The base module for Flares
    //   All page apps should extend off this module
    // ==================================

    var flaresBase = angular.module('flaresBase');
    
    // =================
    // Register constants
    // 1. Base API root (especially for non-domain-root apps)
    // 2. Laravel CSRF Token, for laravel_token API auth
    // 3. Flares Resource Definitions - A source of truth for factories/services
    // =================

    var base = angular.element('base');
    flaresBase.constant('flApiPathRoot', base ? (base.data('href') || base.attr('href')) : '');

    flaresBase.constant('flLaravelCsrfToken', window.csrfToken || angular.element('meta[name="csrf-token"]').attr('content'));

    flaresBase.constant('flResourceDefinitions', {
        refData: {
            apiBase: '/api/refdata',
            singular: 'refdata',
            plural: 'refdata',
            aliases: [{
                activity: ['dashboard', 'activity']
            }]
        },
        dashboard: {
            apiBase: '/api/dashboard',
            singular: 'dashboard',
            plural: 'dashboard'
        },
        member: {
            apiBase: '/api/member',
            singular: 'member',
            plural: 'members',
            nestedResources: ['posting', 'picture', 'status', 'decoration'],
            aliases: [{
                search: ['members', 'search']
            }]
        },
        activity: {
            apiBase: '/api/activity',
            singular: 'activity',
            plural: 'activities',
            nestedResources: ['roll', 'awol'],
            aliases: [{
                roll: ['activity', 'roll'],
                awol: ['activities', 'awol']
            }],
        },
        decoration: {
            apiBase: '/api/decoration',
            singular: 'decoration',
            plural: 'decorations',
            aliases: [{
                search: ['decorations', 'search']
            }]
        },
        approval: {
            apiBase: '/api/approval',
            singular: 'approval',
            plural: 'approvals',
            aliases: [{
                pending: ['approval', 'pending']
            }]
        },
        user: {
            apiBase: '/api/users',
            singular: 'user',
            plural: 'users',
        },
    });

}());