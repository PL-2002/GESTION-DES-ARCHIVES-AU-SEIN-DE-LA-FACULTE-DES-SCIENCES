<?php

return [

/*
 * The service account key JSON file contents.
 *
 * You should provide a string that is the contents of a service account
 * key JSON file that you created in the Google Cloud Console.
 */
'service_account_key' => env('GOOGLE_CLOUD_STORAGE_JSON_KEY'),

/*
 * The bucket to be used by the Google Cloud Storage filesystem.
 */
'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),

/*
 * The URI of the file you want to use as a credentials file.
 */
'path_to_credentials_json' => env('GOOGLE_APPLICATION_CREDENTIALS'),

/*
 * The project name.
 */
'project_id' => env('GOOGLE_CLOUD_STORAGE_PROJECT_ID', null),

/*
 * Optional: Use a custom storage API URI
 */
'storage_api_uri' => env('GOOGLE_CLOUD_STORAGE_API_URI', null),
];
