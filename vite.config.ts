import { defineConfig } from 'vite'
import tailwindcss from 'tailwindcss'
import autoprefixer from 'autoprefixer'
import laravell from 'vite-plugin-laravel'
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import inertia from './resources/scripts/vite/inertia-layout'
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default defineConfig({
	plugins: [
		inertia(),
		vue(),
		laravell({
			postcss: [
				tailwindcss(),
				autoprefixer(),

			],
		}),
		laravel({

			input: [
				'resources/scripts/main.ts',
				'resources/views/vendors/css/**'
				
			],
			refresh: true
		},
			
	),
	viteStaticCopy({
		targets:[
			{
				src: 'resources/vendors',
				dest: '../'
			},
			{
				src: 'resources/css',
				dest: '../'
			},
			{
				src: 'resources/js',
				dest: '../'
			},
			{
				src: 'resources/images',
				dest: '../'
			}
		]
		
	})
		
	],

})
