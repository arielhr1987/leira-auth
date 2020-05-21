<?php


class Leira_Auth_Form_Element extends DOMElement{


	/**
	 * @param $selector
	 *
	 * @return array|DOMNodeList
	 */
	public function query( $selector ) {

		if ( $this->ownerDocument instanceof Leira_Auth_Form ) {
			return $this->ownerDocument->query( $selector, $this );
		}

		return array();
	}

	public function __toString() {
		if ( $this->ownerDocument instanceof Leira_Auth_Form ) {
			return $this->ownerDocument->saveHTML( $this );
		}

		return '';
	}
}