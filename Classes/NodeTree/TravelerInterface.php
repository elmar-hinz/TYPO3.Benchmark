<?php

namespace ElmarHinz\NodeTree;

interface TravelerInterface
{
	function onDown($object);
	function onUp($object);
}


