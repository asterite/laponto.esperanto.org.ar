<?
PHP::requireClasses('Session');

define('CART_SESSION_NAME', '_the_cart_');

/**
 * A cart that can hold any kind of object as a product, as long as each product is identified
 * by an ID.
 * A cart is maintained across a user session. You can retrieve it and modifiy it
 * with the static methods getSessionCart() and setSessionCart().
 *
 * @package E-Commerce
 */
class Cart {

	/**
	 * @access private
	 */
	var $items;

	/**
	 * Constructs an empty cart.
	 */
	function Cart() {
		$this->items = array();
	}

	/**
	 * Adds a product to the cart.
	 * @param mixed $id the id of the product
	 * @param mixed $product the product
	 * @param integer $quantity the quantity to add
	 */
	function add($id, $product, $quantity = 1) {
		if ($this->items[$id]) {
			$this->items[$id]->quantity += $quantity;
		} else {
			$this->items[$id] = new CartItem($id, $product, $quantity);
		}
	}

	/**
	 * Sets the quantity of a product in this cart.
	 * @param mixed $id  the id of the product
	 * @param integer $quantity the new quantity (zero removes the product)
	 */
	function set($id, $quantity) {
		if ($quantity == 0) {
			$this->remove($id);
		} else {
			$this->items[$id]->quantity = $quantity;
		}
	}

	/**
	 * Removes a product from this cart.
	 * @param mixed $id the id of the product
	 */
	function remove($id) {
		unset($this->items[$id]);
	}

	/**
	 * Returns the items of this cart.
	 * @return CartItem[] the items in an array
	 */
	function getItems() {
		return $this->items;
	}

	/**
	 * Returns the total number of items (sum of the quantity of each item)
	 * in this cart.
	 */
	function getItemsCount() {
		$q = 0;
		foreach($this->items as $item) {
			$q += $item->quantity;
		}
		return $q;
	}

	/**
	 * Returns the cart in the session. Returns an empty cart if no cart
	 * was in the session.
	 * @return Cart the cart in the session
	 * @static
	 */
	function getSessionCart() {
		$cart = Session::getAttribute(CART_SESSION_NAME);
		if (is_null($cart)) $cart = new Cart();
		return $cart;
	}

	/**
	 * Sets the cart in the session.
	 * @param Cart $cart the Cart to set
	 * @static
	 */
	function setSessionCart($cart) {
		Session::setAttribute(CART_SESSION_NAME, $cart);
	}

}

/**
 * Represents an item (with its quantity) in the cart.
 * You obtain objects of this type from a Cart.
 *
 * @package E-Commerce
 */
class CartItem {

	/**
	 * The id of the item
	 */
	var $id;
	/**
	 * The product of the item
	 */
	var $product;
	/**
	 * The quantity of the item
	 * @var integer
	 */
	var $quantity;

	/**
	 * @access private
	 */
	function CartItem($id, $product, $quantity) {
		$this->id = $id;
		$this->product = $product;
		$this->quantity = $quantity;
	}

}
?>