<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-08-13
 * Time: 8:39 PM
 */
/*	Copyright 2014 Patrick Mauro (email : patrick@mauro.ca)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should receive a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St., Fifth Floor, Boston, MA 02110-1301 USA
*/

namespace droppedbars\datastructure;

/**
 * Class DoubleLinkedList
 * @package droppedbars\datastructure
 */
class DoubleLinkedList {
	protected $previous;
	protected $next;
	protected $payload;

	public function __construct( $payload ) {
		$this->previous = null;
		$this->next     = null;
		$this->payload  = $payload;
	}

	/**
	 * Returns the contents of the node
	 * @return mixed
	 */
	public function payload() {
		return $this->payload;
	}

	/**
	 * Create a node with the provided payload and insert it into the linked list before the current node.
	 * Returns the new node.
	 *
	 * @param $newPayload
	 *
	 * @return DoubleLinkedList
	 */
	public function insertBefore( $newPayload ) {
		$node        = new DoubleLinkedList( $newPayload );
		$oldPrevious = $this->previous;

		$node->next     = $this;
		$this->previous = $node;

		if ( !is_null( $oldPrevious ) ) {
			$oldPrevious->next = $node;
			$node->previous    = $oldPrevious;
		}

		return $node;
	}

	/**
	 * Create a node with the provided payload and insert it into the linked list after the current node.
	 * Returns the new node.
	 *
	 * @param $newPayload
	 *
	 * @return DoubleLinkedList
	 */
	public function insertAfter( $newPayload ) {
		$node    = new DoubleLinkedList( $newPayload );
		$oldNext = $this->next;

		$this->next     = $node;
		$node->previous = $this;

		if ( !is_null( $oldNext ) ) {
			$oldNext->previous = $node;
			$node->next        = $oldNext;
		}

		return $node;
	}

	/**
	 * Removes the next node in the linked list and links to or from it.
	 * If another node is attached as the next next node, it will become the new next node.
	 */
	public function removeNext() {
		$next = $this->next;
		if ( !is_null( $next ) ) {
			$nextNext   = $next->next;
			$this->next = $nextNext;
			if ( !is_null( $nextNext ) ) {
				$nextNext->previous = $this;
			}
			$next->previous = null;
			$next->next     = null;
		}
	}

	/**
	 * Removes the previous node in the linked list and links to or from it.
	 * If another node is attached as the previous previous node, it will become the new previous node.
	 */
	public function removePrevious() {
		$previous = $this->previous;
		if ( !is_null( $previous ) ) {
			$previousPrevious = $previous->previous;
			$this->previous   = $previousPrevious;
			if ( !is_null( $previousPrevious ) ) {
				$previousPrevious->next = $this;
			}
			$previous->previous = null;
			$previous->next     = null;
		}
	}

	/**
	 * Returns a reference to the tail node in the linked list to which this node belongs.
	 * If this node is the tail, it will return itself.
	 * @return DoubleLinkedList
	 */
	public function tail() {
		$next = $this;
		while ( !is_null( $next->next ) ) {
			$next = $next->next;
		}

		return $next;
	}

	/**
	 * Returns the number of nodes contained within the linked list
	 * @return int
	 */
	public function count() {
		$node    = $this->head();
		$counter = 1;
		while ( !is_null( $node->next ) ) {
			$counter ++;
			$node = $node->next;
		}

		return $counter;
	}

	/**
	 * Returns a reference to the head node in the linked list to which this node belongs.
	 * If this node is the head, it will return itself.
	 * @return DoubleLinkedList
	 */
	public function head() {
		$previous = $this;
		while ( !is_null( $previous->previous ) ) {
			$previous = $previous->previous;
		}

		return $previous;
	}

	/**
	 * Returns the node next to this one, or null
	 * @return DoubleLinkedList|null
	 */
	public function next() {
		return $this->next;
	}

	/**
	 * Returns the node previous to this one, or null
	 * @return DoubleLinkedList|null
	 */
	public function previous() {
		return $this->previous;
	}

}