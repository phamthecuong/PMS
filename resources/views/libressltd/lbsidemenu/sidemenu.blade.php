<?php
	function show_sub($item, $work_planning) {
		if (!$item->available())
		{
			return "";
		}
		$string = "";
		$title = ($item->title_translated == 1) ? trans($item->title) : $item->title;
		if ($item->children->count() == 0)
		{
			if (isset($item->parent))
			{
				$string .= view("layouts.elements.sidebar_item_single", ["title" => $title, "icon" => $item->icon, "url" => $item->url, "id" => $item->id_string, "is_child" => true]);
			}
			else
			{
				$string .= view("layouts.elements.sidebar_item_single", ["title" => $title, "icon" => $item->icon, "url" => $item->url, "id" => $item->id_string]);
			}
		}
		else
		{
			$string .= view("layouts.elements.sidebar_item_multi_open", ["title" => $title, "icon" => $item->icon, "id" => $item->id_string]);
			foreach ($item->children as $child)
			{
				$string .= show_sub($child, $work_planning);
				if ($child->id_string == "work_planning_start_new_process")
				{
					foreach ($work_planning as $wp_item)
					{
						$string .= view("layouts.elements.sidebar_item_single", ["title" => $wp_item['title'], "icon" => $wp_item['icon'], "url" => $wp_item['url'], "id" => $wp_item['id'], "is_child" => true, "class" => "wp_history_item"]);
					}
				}
			}
			$string .= view("layouts.elements.sidebar_item_multi_close");
		}
		return $string;
	}

	$root_items = App\Models\LBSM_item::whereNull("parent_id")->with("children", "parent", "roles", "permissions")->orderBy("order_number")->get();
	$wp_data = App\Models\tblWorkPlanning::getDataHaveList();
?>

@foreach ($root_items as $item)
	{!! show_sub($item, $wp_data) !!}
@endforeach

@push("css")
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('/fontawesome-picker/css/fontawesome-iconpicker.css') }}">
@endpush

@push("script")
<script src="{{ asset('/fontawesome-picker/js/fontawesome-iconpicker.js') }}"></script>
@endpush