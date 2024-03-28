import vue from "@vitejs/plugin-vue";
import { resolve } from "path";
import { defineConfig } from "vite";
import { fileURLToPath, URL } from "url";

export default defineConfig({
    plugins: [vue()],

    define: {
        "process.env": process.env, // Vite ditched process.env, so we need to pass it in
    },

    resolve: {
        alias: [
            { find: '@', replacement: fileURLToPath(new URL('./resources/js', import.meta.url)) },
            { find: '@vendor', replacement: fileURLToPath(new URL('./vendor', import.meta.url)) },
        ]
    },

    build: {
        outDir: resolve(__dirname, "dist"),
        emptyOutDir: true,
        target: "ES2022",
        minify: true,
        manifest: true,
        lib: {
            entry: resolve(__dirname, "resources/js/field.js"),
            name: "field",
            formats: ["umd"],
            fileName: () => "js/field.js",
        },
        rollupOptions: {
            input: resolve(__dirname, "resources/js/field.js"),
            external: ["vue", "nova-filemanager", "laravel-nova"],
            output: {
                globals: {
                    vue: "Vue",
                    nova: "Nova",
                    "laravel-nova": "LaravelNova",
                    "nova-filemanager": "NovaFileManager"
                },
                assetFileNames: "css/field.css",
            },
        },
    },

    optimizeDeps: {
        include: ["vue", "@inertiajs/inertia", "@inertiajs/inertia-vue3", "axios"],
    },
});
