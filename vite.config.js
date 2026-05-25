import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/dashboard.css',
                'resources/js/dashboard.js',
                'resources/css/auth/login.css',
                'resources/js/auth/login.js',
                'resources/css/superadmin/dashboard.css',
                'resources/css/superadmin/users.css',
                'resources/css/superadmin/users-edit.css',
                'resources/css/superadmin/menus.css',
                'resources/js/superadmin/dashboard.js',
                'resources/js/superadmin/users.js',
                'resources/js/superadmin/menus.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
