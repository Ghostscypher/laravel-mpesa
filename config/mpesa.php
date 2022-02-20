<?php

return [

    /*
    |--------------------------------------------------------------------------
    | A list of services offered by the website at this URL
    |--------------------------------------------------------------------------
    |
    | Default service to be used
    |
    */
    'default' => 'service_1',

    /*
    |--------------------------------------------------------------------------
    | A list of services offered by the website at this URL
    |--------------------------------------------------------------------------
    |
    | This enables the package to be used in a cascading manner allowing more one than
    | configurable website
    |
    */
    'services' => [
        'service_1' => [

            /*
            |--------------------------------------------------------------------------
            | Indicated the type of tenant this is
            |--------------------------------------------------------------------------
            |
            | supported: "main", "client", "service"
            |   main: the tenant controlling service(s)
            |   client: the tenant being controlled by main 
            |   service: the services created by the client
            */
            'type' => 'main',

            /*
            |--------------------------------------------------------------------------
            | Whether or not tasks should be queued
            |--------------------------------------------------------------------------
            |
            | This defines if tasks should be queued 
            |
            */
            'should_queue' => true,
            'action_queue_class' => \Ghostscypher\CDP\Jobs\ExecuteActionJob::class,

            /*
            |--------------------------------------------------------------------------
            | Service tasks available together with their action classes
            |--------------------------------------------------------------------------
            |
            | This is a list of tasks available together with their action classes
            | The status define the state of the system and what actions can be undertaken
            |
            | default: "create", "destroy", "suspend", "activate"
            |
            | NB:
            |   The action class must implement the Ghostscypher\CDP\Contracts\CDPActionContract interface
            |   The hook class must implement the Ghostscypher\CDP\Contracts\CDPHookContract
            |
            */
            'tasks' => [
                'create' => [
                    'action_class' => \Ghostscypher\CDP\Actions\CreateAction::class,
                    // 'should_queue' => true, // Indicates whether this action should be queued
                ],
                'destroy' => [
                    'action_class' => \Ghostscypher\CDP\Actions\DestroyAction::class,
                ],
                'activate' => [
                    'action_class' => \Ghostscypher\CDP\Actions\ActivateAction::class,
                ],
                'suspend' => [
                    'action_class' => \Ghostscypher\CDP\Actions\SuspendAction::class,
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Models to be used by the system
            |--------------------------------------------------------------------------
            |
            | This defines the models used by the system enabling the package
            | to be extended
            | TODO: Add support for this
            */
            'models' => [
                'deployment' => \Ghostscypher\CDP\Models\Deployment::class,
                'log' => \Ghostscypher\CDP\Models\Log::class,
                'service' => \Ghostscypher\CDP\Models\Service::class,
                'credential' => \Ghostscypher\CDP\Models\Credential::class,
            ],
        ],
    ],
];
