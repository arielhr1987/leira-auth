const fs = require( 'fs' );
const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const srcDir = path.resolve( __dirname, 'src' );

function getBlockEntries() {
	const entries = {};

	if ( ! fs.existsSync( srcDir ) ) {
		return entries;
	}

	const sourceFolders = fs.readdirSync( srcDir, { withFileTypes: true } );

	for ( const folder of sourceFolders ) {
		if ( ! folder.isDirectory() ) {
			continue;
		}

		const blockDir = path.join( srcDir, folder.name );
		const blockJson = path.join( blockDir, 'block.json' );
		if ( ! fs.existsSync( blockJson ) ) {
			continue;
		}

		const editEntry = path.join( blockDir, 'edit.js' );
		const indexEntry = path.join( blockDir, 'index.js' );
		const styleEntry = path.join( blockDir, 'style.scss' );
		const editorStyleEntry = path.join( blockDir, 'editor.scss' );

		if ( fs.existsSync( editEntry ) ) {
			entries[ `${ folder.name }/edit` ] = editEntry;
		} else if ( fs.existsSync( indexEntry ) ) {
			entries[ `${ folder.name }/index` ] = indexEntry;
		}

		if ( fs.existsSync( styleEntry ) ) {
			entries[ `${ folder.name }/style` ] = styleEntry;
		}

		if ( fs.existsSync( editorStyleEntry ) ) {
			entries[ `${ folder.name }/editor` ] = editorStyleEntry;
		}
	}

	return entries;
}

function getRootEntries() {
	const entries = {};
	const formsEntry = path.join( srcDir, 'forms.js' );

	if ( fs.existsSync( formsEntry ) ) {
		entries.forms = formsEntry;
	}

	return entries;
}

module.exports = {
	...defaultConfig,
	entry: {
		...getRootEntries(),
		...getBlockEntries(),
	},
	optimization: {
		...defaultConfig.optimization,
		splitChunks: {
			...defaultConfig.optimization?.splitChunks,
			cacheGroups: {
				...defaultConfig.optimization?.splitChunks?.cacheGroups,
				style: {
					...defaultConfig.optimization?.splitChunks?.cacheGroups?.style,
					name: ( _, chunks ) => chunks[0].name,
				},
			},
		},
	},
	output: {
		...defaultConfig.output,
		filename: '[name].js',
		path: path.resolve( __dirname, 'build' ),
	},
};
