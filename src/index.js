import {registerBlockType} from '@wordpress/blocks';
import Login from './login';

/**
 * Register the Login block
 */
// registerBlockType('leira-auth/login', {
// 	apiVersion: 2,
// 	title: 'Login Form',
// 	icon: 'lock',
// 	category: 'widgets',
// 	attributes: {
// 		redirect: {type: 'string', default: ''},
// 		form_id: {type: 'string', default: 'loginform'},
// 		label_username: {type: 'string', default: 'Username or Email'},
// 		label_password: {type: 'string', default: 'Password'},
// 		label_remember: {type: 'string', default: 'Remember Me'},
// 		label_log_in: {type: 'string', default: 'Log In'},
// 		// id_username: {type: 'string', default: 'user_login'},
// 		// id_password: {type: 'string', default: 'user_pass'},
// 		// id_remember: {type: 'string', default: 'rememberme'},
// 		// id_submit: {type: 'string', default: 'wp-submit'},
// 		remember: {type: 'boolean', default: true},
// 		// value_remember: {type: 'boolean', default: false},
// 	},
// 	edit: Login,
// 	save: () => null, // Dynamic block
// });


