<?
class ObjectDefinitionParser {
	protected $data = array ();
	protected $depth = array ();
	protected $file = "";

	public function ObjectDefinitionParser($classname) {
		$this->file = './objects/'.strtolower($classname).'.xml';
	}

	protected function startElement($parser, $name, $attrs) {
		@$this->depth[$parser]++;
		@$this->data[$name][$attrs['NAME']] = $attrs;
	}

	protected function endElement($parser, $name) {
		$this->depth[$parser]--;
	}

	public function parse() {
		$xml_parser = xml_parser_create();
		xml_set_object($xml_parser, $this);
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		if (!($fp = fopen($this->file, "r"))) {
			die("could not open XML input: ".$this->file);
		}

		while ($data = fread($fp, 4096)) {
			if (!xml_parse($xml_parser, $data, feof($fp))) {
				die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
			}
		}
		return $this->data;
	}
}