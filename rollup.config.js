import path from 'path';
import { fileURLToPath } from 'url';

import { defineConfig } from 'rollup';
import scss from 'rollup-plugin-scss';
import babel from '@rollup/plugin-babel';
import alias from '@rollup/plugin-alias';
import terser from '@rollup/plugin-terser';
import resolve from '@rollup/plugin-node-resolve';

import postcss from 'postcss';
import cssnano from 'cssnano';
import autoprefixer from 'autoprefixer';
import postcssRTLCSS from 'postcss-rtlcss';
import { Mode, Source } from 'postcss-rtlcss/options';

/**
 * Define extensions to be resolved via alias.
 *
 * @since 1.0.0
 */
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname);

const resolveExtensions = resolve({
	extensions: ['.mjs', '.js', '.jsx', '.sass', '.scss'],
});

const resolveAlias = alias({
	entries: [
		{
			find: 'src',
			replacement: path.resolve(rootDir, './public/assets/src/'),
		},
	],
	resolveExtensions,
});

/**
 * Prepare global options.
 * Holds path & name of source and destination assets.
 *
 * @since 1.0.0
 */
const assets = {
	script: {
		global: {
			src: './public/assets/src/import.global.js',
			build: './public/assets/build/global.min.js',
		},
		common: {
			src: './public/assets/src/import.common.js',
			build: './public/assets/build/common.min.js',
		},
		guest: {
			src: './public/assets/src/import.guest.js',
			build: './public/assets/build/guest.min.js',
		},
		private: {
			src: './public/assets/src/import.private.js',
			build: './public/assets/build/private.min.js',
		},
	},
	scss: {
		src: './public/assets/src/scss',
		build: './public/assets/build/public.min.css',
		buildName: 'public.min.css',
	},
};

/**
 * Define plugins once and reuse them.
 */
const plugins = [
	resolve(),
	babel({ presets: ['@babel/preset-env'], babelHelpers: 'bundled' }),
	terser(),
	scss({
		output: assets.scss.build,
		fileName: assets.scss.buildName,
		watch: assets.scss.src,
		verbose: true,
		sourceMap: false,
		failOnError: false,
		processor: async () =>
			postcss([
				autoprefixer(),
				postcssRTLCSS({
					mode: Mode.override,
					source: Source.ltr,
				}),
				cssnano(),
			]),
	}),
	resolveAlias,
];

/**
 * Generate multiple script configurations dynamically.
 */
const scripts = Object.entries(assets.script).map(([key, value]) => ({
	input: value.src,
	output: {
		file: value.build,
		name: key,
		format: "umd", // "iife", "umd", "amd", "cjs" "esm"
	},
}));

export default defineConfig(
	scripts.map((config) => ({
		...config,
		plugins,
	}))
);
