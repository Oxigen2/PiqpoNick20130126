<?php

class FormGenerator
{
	var $output;
	var $formName;
	var $promptSpacer;
	var $lineSpacer;
	var $itemClass;
	var $promptClass;
	var $inputClass;

	// $action should be the link to which the form data will be sent.
	// $method should be post or get
	function FormGenerator($link, $method, $promptSpacer = "<br>", $lineSpacer = "<p>", $formClass = "", $itemClass = "", $promptClass = "", $inputClass = "")
	{
		$this->formName = "formName";
		$this->promptSpacer = $promptSpacer;
		$this->lineSpacer   = $lineSpacer;
		$this->itemClass    = (strlen($itemClass) == 0)   ? "" : "<div class=$itemClass>";
		$this->promptClass  = (strlen($promptClass) == 0) ? "" : "<div class=$promptClass>";
		$this->inputClass   = (strlen($inputClass) == 0)  ? "" : "<div class=$inputClass>";

		$formClass = (strlen($formClass) == 0) ? "" : "class=$formClass";
		
		$this->output = "<form {$formClass} action=\"{$link}\" method=\"{$method}\" name=\"formName\">\n";
	}
	
	private function addItem($prompt, $input)
	{
		$this->output .= $this->itemClass;
		
		$this->output .= $this->promptClass;
		$this->output .= $prompt;	
		$this->output .= (strlen($this->promptClass) == 0) ? "" : "</div>";		
		$this->output .= $this->promptSpacer."\n";
		
		$this->output .= $this->inputClass;
		$this->output .= $input;
		$this->output .= (strlen($this->inputClass) == 0) ? "" : "</div>";
		$this->output .= $this->lineSpacer."\n";
		
		$this->output .= (strlen($this->itemClass) == 0) ? "" : "</div>\n";
	}
	
	function getOutput()
	{
		$this->output .= "</form>\n";
		return $this->output;
	}
	
	// Adds just plain text.
	function addPrompt ($prompt, $text)
	{
		$this->addItem($prompt, $text);
	}
	
	function addTextArea ($prompt, $varName, $content, $width, $rows)
	{
		$this->addItem($prompt, "<textarea name=\"{$varName}\" style='width:{$width}px;' rows={$rows}>{$content}</textarea>");
	}
	
	function addText ($prompt, $varName, $content, $width)
	{
		$this->addItem($prompt, "<input type=text style='width:{$width}px;' name=\"{$varName}\" id=\"{$varName}\" value=\"{$content}\">");
	}
	
	function addPassword ($prompt, $varName, $content, $width)
	{
		$this->addItem($prompt, "<input type=password style='width:{$width}px;' name=\"{$varName}\" id=\"{$varName}\" value=\"{$content}\">");
	}
	
	function addCheckbox ($prompt, $varName, $value, $checked)
	{
		$checkedText = $checked ? "checked" : "";
		$this->addItem($prompt, "<input type=\"checkbox\" name=\"$varName\" value=\"$value\" $checkedText />");
	}
	
	function addCheckedText ($checkPrompt, $textPrompt, $checkedName, $textName, $content)
	{
		$checked = ($content) ? "" : "checked";
		
		$this->output .= "{$checkPrompt}<input type=checkbox {$checked} name={$checkedName} onclick=\"document.formName.{$textName}.disabled = document.formName.{$checkedName}.checked\">";
		$this->output .= "{$textPrompt} <input type=text size=20 name={$textName} value={$content}>";

		$this->output .= "<script language=\"JavaScript\">";
		$this->output .= "document.formName.{$textName}.disabled = document.{$formName}.{$checkedName}.checked";
		$this->output .= "</script>";
		$this->output .= "$this->lineSpacer";
	}
	
	function addHidden($name, $value)
	{
		$this->output .= "<input type=hidden name=\"{$name}\"  value=\"{$value}\" />\n";
	}
	
	// $values must be array of value => text
	// $selected should be the selected value
	function addDropdown($prompt, $values, $name, $selected, $size)
	{
		$dropdown = "<select name=\"{$name}\" size={$size}>\n";
		foreach ($values as $value => $text)
		{
			if ($selected == $value)
			{
				$sel = "selected";
			}
			else
			{
				$sel = "";
			}
			$dropdown .= "<option value=\"{$value}\" {$sel}>{$text}</option>\n";
		}
		$dropdown .= "</select>\n";
		
		$this->addItem($prompt, $dropdown);
	}
	
	// $values must be array of value => text
	function addRadio($prompt, $values, $name, $selected)
	{
		$radio = "";
		foreach ($values as $value => $text)
		{
			if ($selected == $value)
			{
				$sel = "checked";
			}
			else
			{
				$sel = "";
			}
			$radio .= "<input {$sel} type=\"radio\" name=\"{$name}\" value={$value}>{$text}<br>";
		}
		$this->addItem($prompt, $radio);
	}
	
	function addSubmit($value, $name)
	{
		$this->addItem("", "<input type=submit name=\"{$name}\" value=\"{$value}\" />");
	}
	
	function addReset($value)
	{
		$this->addItem("", "<input type=reset value=\"{$value}\" />");
	}		
}

?>