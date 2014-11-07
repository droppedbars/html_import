<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-08-27
 * Time: 9:09 PM
 */

namespace droppedbars\datastructure;

require_once dirname( __FILE__ ) . "/DoubleLinkedList.php";


/**
 * Class LinkedTreeException
 * Wrapper exception for anything that occurs within the LinkedTree that cannot be allowed.
 * @package droppedbars\datastructure
 */
class LinkedTreeException extends \Exception {
}

/**
 * Class ChildPayloadNotLinkedTreeException
 * Represents exceptions where a child is supposed to be a LinkedTree but is not.
 * @package droppedbars\datastructure
 */
class ChildPayloadNotLinkedTreeException extends LinkedTreeException {
}

/**
 * Class LinkedTree
 * Datastructure that provides bidirectonal linkage from parent to children, and all the children to each sibling so that the ordering of children is preserved.
 * @package droppedbars\datastructure
 */
class LinkedTree {
	protected $parent;
	protected $children;
	protected $payload;
	protected $childIterator;

	/**
	 * @param $payload object to be stored within the node
	 */
	public function __construct( $payload ) {
		$this->parent   = null;
		$this->children = null;
		$this->payload  = $payload;
	}

	/**
	 * Returns the parent of the this node.
	 * @return null|LinkedTree
	 * @throws ChildPayloadNotLinkedTreeException
	 */
	public function getParent() {
		$parent = $this->parent;
		if ( !is_null( $parent ) ) {
			if ( !$parent instanceof LinkedTree ) {
				throw new ChildPayloadNotLinkedTreeException();
			}

			return $this->parent;
		}

		return null;
	}

	/**
	 * Appends a node to the end of the children attached to this node.
	 *
	 * @param LinkedTree $newChild
	 */
	public function addChild( LinkedTree $newChild ) {
		$newChild->parent = $this;
		if ( is_null( $this->children ) ) {
			$this->children      = new DoubleLinkedList( $newChild );
			$this->childIterator = $this->children;
		} else {
			$this->children->tail()->insertAfter( $newChild );
		}
	}

	/**
	 * Returns the first child of this node, or null if there are no children.  It also ensures that any call to getChild() will return the first child of this node as well.
	 * @return null|LinkedTree the first child of this node.
	 * @throws ChildPayloadNotLinkedTreeException Child being returned is not a LinkedTree object
	 */
	public function headChild() {
		if ( is_null( $this->children ) ) {
			return null;
		} else {
			$this->childIterator = $this->children->head();
			$payload             = $this->childIterator->payload();
			if ( !is_null( $payload ) ) {
				if ( !$payload instanceof LinkedTree ) {
					throw new ChildPayloadNotLinkedTreeException();
				}
			}

			return $payload;
		}
	}

	/**
	 * Returns the last child of this node, or null if there are no children.  It also ensures that any call to getChild() will return the last child of this node as well.
	 * @return null|LinkedTree the last child of this node
	 * @throws ChildPayloadNotLinkedTreeException Child being returned is not a LinkedTree object
	 */
	public function tailChild() {
		if ( is_null( $this->children ) ) {
			return null;
		} else {
			$this->childIterator = $this->children->tail();
			$payload             = $this->childIterator->payload();
			if ( !is_null( $payload ) ) {
				if ( !$payload instanceof LinkedTree ) {
					throw new ChildPayloadNotLinkedTreeException();
				}
			}

			return $payload;
		}
	}

	/**
	 * Returns the next child of this node, or null if there are no children or it has attempted to move beyond the last child of this node.  It also ensures that any call to getChild() will return the returned child as well.
	 * @return null|LinkedTree the next child of this node
	 * @throws ChildPayloadNotLinkedTreeException Child being returned is not a LinkedTree object
	 */
	public function nextChild() {
		if ( is_null( $this->children ) ) {
			return null;
		} else {
			if ( is_null( $this->childIterator ) ) {
				return null;
			}
			$this->childIterator = $this->childIterator->next();
			if ( is_null( $this->childIterator ) ) {
				return null;
			} else {
				$payload = $this->childIterator->payload();
				if ( !is_null( $payload ) ) {
					if ( !$payload instanceof LinkedTree ) {
						throw new ChildPayloadNotLinkedTreeException();
					}
				}

				return $payload;
			}
		}
	}

	/**
	 * Returns the previous child of this node, or null if there are no children or it has attempted to move beyond the first child of this node.  It also ensures that any call to getChild() will return the returned child as well.
	 * @return null|LinkedTree the previous child of this node
	 * @throws ChildPayloadNotLinkedTreeException Child being returned is not a LinkedTree object
	 */
	public function previousChild() {
		if ( is_null( $this->children ) ) {
			return null;
		} else {
			if ( is_null( $this->childIterator ) ) {
				return null;
			}
			$this->childIterator = $this->childIterator->previous();
			if ( is_null( $this->childIterator ) ) {
				return null;
			} else {
				$payload = $this->childIterator->payload();
				if ( !is_null( $payload ) ) {
					if ( !$payload instanceof LinkedTree ) {
						throw new ChildPayloadNotLinkedTreeException();
					}
				}

				return $payload;
			}
		}
	}

	/**
	 * Removes the child that is current retrievable via getChild().  It ensures the child does not have a reference to this node, and this node maintains no reference to chat child.  If it was the only child then getChild() will now return null.  If it was the first child, getChild() will return what was the second child (or null).  Otherwise getChild() will no return the child that came before the one being removed.
	 */
	public function removeChild() {
		if ( !is_null( $this->childIterator ) ) {
			if ( $this->childIterator === $this->children ) {
				$this->children = $this->childIterator->next();
			}
			if ( !is_null( $this->childIterator->previous() ) ) {
				$this->childIterator->payload()->parent = null;
				$this->childIterator                    = $this->childIterator->previous();
				$this->childIterator->removeNext();
			} else {
				if ( !is_null( $this->childIterator->next() ) ) {
					$this->childIterator->payload()->parent = null;
					$this->childIterator                    = $this->childIterator->next();
					$this->childIterator->removePrevious();
				} else { // it was an only child
					$this->childIterator = null;
					$this->children      = null;
				}
			}
		}
	}

	/**
	 * Returns the current child of this node.
	 * @return null|LinkedTree the current child of this node
	 * @throws ChildPayloadNotLinkedTreeException Child being returned is not a LinkedTree object
	 */
	public function getChild() {
		if ( !is_null( $this->childIterator ) ) {
			$payload = $this->childIterator->payload();

			if ( !is_null( $payload ) ) {
				if ( !$payload instanceof LinkedTree ) {
					throw new ChildPayloadNotLinkedTreeException();
				}
			}

			return $payload;

		} else {
			return null;
		}
	}

	/**
	 * Returns the payload of this node
	 * @return mixed the payload of this node
	 */
	public function payload() {
		return $this->payload;
	}
} 