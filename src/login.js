import {__} from '@wordpress/i18n';
import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import {PanelBody, TextControl, ToggleControl} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import {registerBlockType} from '@wordpress/blocks';

/**
 * Register the Login block
 *
 * @return {JSX.Element}
 * @constructor
 */
registerBlockType('leira-auth/login', {
	/**
	 * Block editor
	 *
	 * @param {object} attributes - The attributes of the block
	 * @param {string} attributes.redirect - Register URL
	 * @param {string} attributes.form_id - Form ID
	 * @param {string} attributes.label_username - Username label
	 * @param {string} attributes.username_group_class - Username group class
	 * @param {string} attributes.username_label_class - Username label class
	 * @param {string} attributes.id_username - Username input ID
	 * @param {string} attributes.username_class - Username input class
	 * @param {string} attributes.username_placeholder - Username input placeholder
	 * @param {string} attributes.label_password - Password label
	 * @param {string} attributes.password_group_class - Password group class
	 * @param {string} attributes.password_label_class - Password label class
	 * @param {string} attributes.id_password - Password input ID
	 * @param {string} attributes.password_class - Password input class
	 * @param {string} attributes.password_placeholder - Password input placeholder
	 * @param {boolean} attributes.remember - Show "Remember me" checkbox
	 * @param {string} attributes.id_remember - "Remember me" checkbox ID
	 * @param {string} attributes.label_remember - "Remember me" label
	 * @param {boolean} attributes.value_remember - "Remember me" default value (checked or not)
	 * @param {string} attributes.remember_group_class - "Remember me" group class
	 * @param {string} attributes.label_log_in - Log In button label
	 * @param {string} attributes.id_submit - Log In button ID
	 * @param {string} attributes.submit_class - Log In button class
	 * @param {string} attributes.submit_group_class - Log In button group class
	 * @param {boolean} attributes.show_forgot_password - Show "Forgot password" link
	 * @param {string} attributes.forgot_password_label - "Forgot password" link label
	 * @param {boolean} attributes.show_register - Show "Register" link
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
							help={__('URL to redirect to after login. Leave empty to redirect to the same page.', 'leira-auth')}
							onChange={(value) => setAttributes({redirect: value})}
						/>
						<TextControl
							label={__('Form ID', 'login-form-block')}
							value={attributes.form_id}
							onChange={(value) => setAttributes({form_id: value})}
						/>
					</PanelBody>
					<PanelBody title={__('Username', 'leira-auth')} initialOpen={false}>
						<TextControl
							label={__('Label', 'leira-auth')}
							type="text"
							value={attributes.label_username}
							onChange={(value) => setAttributes({label_username: value})}
						/>
						{/*<TextControl*/}
						{/*	label={__('Group class', 'leira-auth')}*/}
						{/*	type="text"*/}
						{/*	value={attributes.username_group_class}*/}
						{/*	onChange={(value) => setAttributes({username_group_class: value})}*/}
						{/*/>*/}
						{/*<TextControl*/}
						{/*	label={__('Label class', 'leira-auth')}*/}
						{/*	type="text"*/}
						{/*	value={attributes.username_label_class}*/}
						{/*	onChange={(value) => setAttributes({username_label_class: value})}*/}
						{/*/>*/}
						<TextControl
							label={__('Input id', 'leira-auth')}
							type="text"
							value={attributes.id_username}
							onChange={(value) => setAttributes({id_username: value})}
						/>
						{/*<TextControl*/}
						{/*	label={__('Input class', 'leira-auth')}*/}
						{/*	type="text"*/}
						{/*	value={attributes.username_class}*/}
						{/*	onChange={(value) => setAttributes({username_class: value})}*/}
						{/*/>*/}
						{/*<TextControl*/}
						{/*	label={__('Input placeholder', 'leira-auth')}*/}
						{/*	type="text"*/}
						{/*	value={attributes.username_placeholder}*/}
						{/*	onChange={(value) => setAttributes({username_placeholder: value})}*/}
						{/*/>*/}
					</PanelBody>
					<PanelBody title={__('Password', 'leira-auth')} initialOpen={false}>
						<TextControl
							label={__('Label', 'leira-auth')}
							value={attributes.label_password}
							onChange={(value) => setAttributes({label_password: value})}
						/>
						{/*<TextControl*/}
						{/*	label={__('Group class', 'leira-auth')}*/}
						{/*	value={attributes.password_group_class}*/}
						{/*	onChange={(value) => setAttributes({password_group_class: value})}*/}
						{/*/>*/}
						{/*<TextControl*/}
						{/*	label={__('Label class', 'leira-auth')}*/}
						{/*	value={attributes.password_label_class}*/}
						{/*	onChange={(value) => setAttributes({password_label_class: value})}*/}
						{/*/>*/}
						<TextControl
							label={__('Input id', 'leira-auth')}
							value={attributes.id_password}
							onChange={(value) => setAttributes({id_password: value})}
						/>
						{/*<TextControl*/}
						{/*	label={__('Input class', 'leira-auth')}*/}
						{/*	value={attributes.password_class}*/}
						{/*	onChange={(value) => setAttributes({password_class: value})}*/}
						{/*/>*/}
						{/*<TextControl*/}
						{/*	label={__('Input placeholder', 'leira-auth')}*/}
						{/*	value={attributes.password_placeholder}*/}
						{/*	onChange={(value) => setAttributes({password_placeholder: value})}*/}
						{/*/>*/}
					</PanelBody>
					<PanelBody title={__('Remember me', 'leira-auth')} initialOpen={false}>
						<ToggleControl
							label={__('Show checkbox', 'login-auth')}
							checked={attributes.remember}
							onChange={(value) => setAttributes({remember: value})}
						/>
						<ToggleControl
							label={__('Checked', 'login-auth')}
							checked={attributes.value_remember}
							onChange={(value) => setAttributes({value_remember: value})}
						/>
						<TextControl
							label={__('Label', 'login-auth')}
							value={attributes.label_remember}
							onChange={(value) => setAttributes({label_remember: value})}
						/>
						{/*<TextControl*/}
						{/*	label={__('Group class', 'login-auth')}*/}
						{/*	value={attributes.remember_group_class}*/}
						{/*	onChange={(value) => setAttributes({remember_group_class: value})}*/}
						{/*/>*/}
					</PanelBody>
					<PanelBody title={__('Log In button', 'login-auth')} initialOpen={false}>
						<TextControl
							label={__('Label', 'login-auth')}
							value={attributes.label_log_in}
							onChange={(value) => setAttributes({label_log_in: value})}
						/>
						<TextControl
							label={__('Id', 'login-auth')}
							value={attributes.id_submit}
							onChange={(value) => setAttributes({id_submit: value})}
						/>
						{/*<TextControl*/}
						{/*	label={__('Class', 'login-auth')}*/}
						{/*	value={attributes.submit_class}*/}
						{/*	onChange={(value) => setAttributes({submit_class: value})}*/}
						{/*/>*/}
						{/*<TextControl*/}
						{/*	label={__('Group class', 'login-auth')}*/}
						{/*	value={attributes.submit_group_class}*/}
						{/*	onChange={(value) => setAttributes({submit_group_class: value})}*/}
						{/*/>*/}
					</PanelBody>
					<PanelBody title={__('Links', 'login-auth')} initialOpen={false}>
						<ToggleControl
							label={__('Show forgot password', 'login-auth')}
							checked={attributes.show_forgot_password}
							onChange={(value) => setAttributes({show_forgot_password: value})}
						/>
						<TextControl
							label={__('Forgot password label', 'login-auth')}
							value={attributes.forgot_password_label}
							onChange={(value) => setAttributes({forgot_password_label: value})}
						/>
						<ToggleControl
							label={__('Show register link', 'login-auth')}
							checked={attributes.show_register}
							onChange={(value) => setAttributes({show_forgot_password: value})}
						/>
						<TextControl
							label={__('Register link label', 'login-auth')}
							value={attributes.register_label}
							onChange={(value) => setAttributes({register_label: value})}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<ServerSideRender block="leira-auth/login" attributes={attributes}/>
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


