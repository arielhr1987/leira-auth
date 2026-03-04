import {__} from '@wordpress/i18n';
import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import {PanelBody, SelectControl, TextControl, ToggleControl} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import {registerBlockType} from '@wordpress/blocks';
import metadata from './block.json';

/**
 * Register the Login block
 *
 * @return {JSX.Element}
 * @constructor
 */
registerBlockType(metadata, {
	/**
	 * Block editor
	 *
	 * @param {object} attributes - The attributes of the block
	 * @param {string} attributes.redirect - Register URL
	 * @param {string} attributes.form_id - Form ID
	 * @param {string} attributes.username_label - Username label
	 * @param {string} attributes.username_placeholder - Username input placeholder
	 * @param {string} attributes.password_label - Password label
	 * @param {string} attributes.password_placeholder - Password input placeholder
	 * @param {boolean} attributes.remember - Show "Remember me" checkbox
	 * @param {string} attributes.remember_label - "Remember me" label
	 * @param {boolean} attributes.remember_default - "Remember me" default value (checked or not)
	 * @param {string} attributes.submit_text - Log In button text
	 * @param {boolean} attributes.forgot_password_show - Show "Forgot password" link
	 * @param {string} attributes.forgot_password_label - "Forgot password" link label
	 * @param {boolean} attributes.register_login - Show "Register" link
	 * @param {string} attributes.register_label - "Register" link label
	 * @param {function} setAttributes - Function to update the block attributes.
	 * @return {JSX.Element}
	 */
	edit: ({attributes, setAttributes}) => {
		// Block props
		const blockProps = useBlockProps();

		/**
		 * Render
		 */
		return (
			<>
				<InspectorControls>
					<PanelBody title={__('General Settings', 'leira-auth')} initialOpen={false}>
						<TextControl
							label={__('Redirect URL', 'leira-auth')}
							type="url"
							value={attributes.redirect}
							help={__('URL to redirect to after login. Leave empty to redirect to the default page.', 'leira-auth')}
							onChange={(value) => setAttributes({redirect: value})}
						/>
					</PanelBody>
					<PanelBody title={__('Username', 'leira-auth')} initialOpen={false}>
						<TextControl
							label={__('Label', 'leira-auth')}
							type="text"
							value={attributes.username_label}
							onChange={(value) => setAttributes({username_label: value})}
						/>
						<TextControl
							label={__('Placeholder', 'leira-auth')}
							type="text"
							value={attributes.username_placeholder}
							onChange={(value) => setAttributes({username_placeholder: value})}
						/>
					</PanelBody>
					<PanelBody title={__('Password', 'leira-auth')} initialOpen={false}>
						<TextControl
							label={__('Label', 'leira-auth')}
							value={attributes.password_label}
							onChange={(value) => setAttributes({password_label: value})}
						/>
						<TextControl
							label={__('Placeholder', 'leira-auth')}
							type="text"
							value={attributes.password_placeholder}
							onChange={(value) => setAttributes({password_placeholder: value})}
						/>
					</PanelBody>
					<PanelBody title={__('Remember me', 'leira-auth')} initialOpen={false}>
						<TextControl
							label={__('Label', 'leira-auth')}
							value={attributes.remember_label}
							placeholder={__('Remember me', 'leira-auth')}
							help={__('The label for remember me checkbox.', 'leira-auth')}
							onChange={(value) => setAttributes({remember_label: value})}
						/>
						<ToggleControl
							label={__('Show checkbox', 'leira-auth')}
							checked={attributes.remember}
							help={__('Show the remember me checkbox.', 'leira-auth')}
							onChange={(value) => setAttributes({remember: value})}
						/>
						<ToggleControl
							label={__('Checked', 'leira-auth')}
							checked={attributes.remember_default}
							help={__('The default checkbox status.', 'leira-auth')}
							onChange={(value) => setAttributes({remember_default: value})}
						/>
					</PanelBody>
					<PanelBody title={__('Log In button', 'leira-auth')} initialOpen={false}>
						<TextControl
							label={__('Text', 'leira-auth')}
							value={attributes.submit_text}
							placeholder={__('Log in', 'leira-auth')}
							help={__('The text of the submit button.', 'leira-auth')}
							onChange={(value) => setAttributes({submit_text: value})}
						/>
						<SelectControl
							label={__('Alignment', 'leira-auth')}
							value={attributes.submit_alignment || 'left'}
							options={[
								{label: __('Left', 'leira-auth'), value: 'left'},
								{label: __('Center', 'leira-auth'), value: 'center'},
								{label: __('Right', 'leira-auth'), value: 'right'},
								{label: __('Full width', 'leira-auth'), value: 'full'},
							]}
							onChange={(value) => setAttributes({submit_alignment: value})}
						/>
					</PanelBody>
					<PanelBody title={__('Links', 'leira-auth')} initialOpen={false}>
						<ToggleControl
							label={__('Show forgot password', 'leira-auth')}
							checked={attributes.forgot_password_show}
							onChange={(value) => setAttributes({forgot_password_show: value})}
						/>
						<TextControl
							label={__('Forgot password label', 'leira-auth')}
							value={attributes.forgot_password_label}
							help={__('The forgot password link text.', 'leira-auth')}
							onChange={(value) => setAttributes({forgot_password_label: value})}
						/>
						<ToggleControl
							label={__('Show register link', 'leira-auth')}
							checked={attributes.register_login}
							onChange={(value) => setAttributes({register_login: value})}
						/>
						<TextControl
							label={__('Register link label', 'leira-auth')}
							value={attributes.register_label}
							onChange={(value) => setAttributes({register_label: value})}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<ServerSideRender block={metadata.name} attributes={attributes}/>
				</div>
			</>
		);
	},
	/**
	 * Save function
	 *
	 * @return {null}
	 */
	save: () => null, // Dynamic block
});
