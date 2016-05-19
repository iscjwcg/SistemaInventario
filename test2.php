<?php
class classParent
{
	public $children = array();

    function classParent() {
        $this->children = array();
    }
	function RegisterChild($childName)
	{
		$this->children["$childName"] = "$childName";
	}
}
class classChild extends classParent
{
    function classChild() {
		parent::classParent();
		self::RegisterChild("test");
		self::RegisterChild("test2");
    }
}
/* self::something();
parent::something(); */
$objChild = new classChild();
foreach($objChild->children as $key => $value)
{
	echo "$key => $value<br />";
}
?>