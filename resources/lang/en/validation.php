<?php

return array (
  'accepted' => 'The :attribute must be accepted.',
  'active_url' => 'The :attribute is not a valid URL.',
  'after' => 'The :attribute must be a date after :date.',
  'alpha' => 'The :attribute may only contain letters.',
  'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
  'alpha_num' => 'The :attribute may only contain letters and numbers.',
  'array' => 'The :attribute must be an array.',
  'before' => 'The :attribute must be a date before :date.',
  'between' => 
  array (
    'numeric' => 'The :attribute must be between :min and :max.',
    'file' => 'The :attribute must be between :min and :max kilobytes.',
    'string' => 'The :attribute must be between :min and :max characters.',
    'array' => 'The :attribute must have between :min and :max items.',
  ),
  'boolean' => 'The :attribute field must be true or false.',
  'confirmed' => 'The :attribute confirmation does not match.',
  'date' => 'The :attribute is not a valid date.',
  'date_format' => 'The :attribute does not match the format :format.',
  'different' => 'The :attribute and :other must be different.',
  'digits' => 'The :attribute must be :digits digits.',
  'digits_between' => 'The :attribute must be between :min and :max digits.',
  'dimensions' => 'The :attribute has invalid image dimensions.',
  'distinct' => 'The :attribute field has a duplicate value.',
  'email' => 'The :attribute must be a valid email address.',
  'exists' => 'The selected :attribute is invalid.',
  'file' => 'The :attribute must be a file.',
  'filled' => 'The :attribute field is required.',
  'image' => 'The :attribute must be an image.',
  'in' => 'The selected :attribute is invalid.',
  'in_array' => 'The :attribute field does not exist in :other.',
  'integer' => 'The :attribute must be an integer.',
  'ip' => 'The :attribute must be a valid IP address.',
  'json' => 'The :attribute must be a valid JSON string.',
  'max' => 
  array (
    'numeric' => 'The :attribute may not be greater than :max.',
    'file' => 'The :attribute may not be greater than :max kilobytes.',
    'string' => 'The :attribute may not be greater than :max characters.',
    'array' => 'The :attribute may not have more than :max items.',
  ),
  'mimes' => 'The :attribute must be a file of type: :values.',
  'mimetypes' => 'The :attribute must be a file of type: :values.',
  'min' => 
  array (
    'numeric' => 'The :attribute must be at least :min.',
    'file' => 'The :attribute must be at least :min kilobytes.',
    'string' => 'The :attribute must be at least :min characters.',
    'array' => 'The :attribute must have at least :min items.',
  ),
  'not_in' => 'The selected :attribute is invalid.',
  'numeric' => 'The :attribute must be a number.',
  'present' => 'The :attribute field must be present.',
  'regex' => 'The :attribute format is invalid.',
  'required' => 'The :attribute field is required.',
  'required_if' => 'The :attribute field is required when :other is :value.',
  'required_unless' => 'The :attribute field is required unless :other is in :values.',
  'required_with' => 'The :attribute field is required when :values is present.',
  'required_with_all' => 'The :attribute field is required when :values is present.',
  'required_without' => 'The :attribute field is required when :values is not present.',
  'required_without_all' => 'The :attribute field is required when none of :values are present.',
  'same' => 'The :attribute and :other must match.',
  'size' => 
  array (
    'numeric' => 'The :attribute must be :size.',
    'file' => 'The :attribute must be :size kilobytes.',
    'string' => 'The :attribute must be :size characters.',
    'array' => 'The :attribute must contain :size items.',
  ),
  'string' => 'The :attribute must be a string.',
  'timezone' => 'The :attribute must be a valid zone.',
  'unique' => 'The :attribute has already been taken.',
  'uploaded' => 'The :attribute failed to upload.',
  'url' => 'The :attribute format is invalid.',
  'custom' => 
  array (
    'attribute-name' => 
    array (
      'rule-name' => 'custom-message',
    ),
    'branch_number' => 
    array (
      'required' => 'The Branch number field is required',
    ),
    'name_vi' => 
    array (
      'required' => 'The Name vi field is required',
    ),
    'road_number' => 
    array (
      'required' => 'The Road number field is required',
    ),
    'name_en' => 
    array (
      'required' => 'The Name en field is required',
    ),
    'cost' => 
    array (
      'digits_between' => 'The cost should be number. maximum is 10000, minimum is 1.',
      1 => 
      array (
        'digits_between' => 'The cost should be number. maximum is 10000, minimum is 1.',
        'min' => 'The cost should be number. maximum is 10000, minimum is 1.',
      ),
      2 => 
      array (
        'digits_between' => 'The cost should be number. maximum is 10000, minimum is 1.',
        'min' => 'The cost should be number. maximum is 10000, minimum is 1.',
      ),
      3 => 
      array (
        'digits_between' => 'The cost should be number. maximum is 10000, minimum is 1.',
        'min' => 'The cost should be number. maximum is 10000, minimum is 1.',
      ),
      4 => 
      array (
        'digits_between' => 'The cost should be number. maximum is 10000, minimum is 1.',
        'min' => 'The cost should be number. maximum is 10000, minimum is 1.',
        'max' => 'The cost should be number. maximum is 10000, minimum is 1.',
      ),
      'required' => 'The cost should be number. maximum is 10000, minimum is 1.',
    ),
    'name_vn' => 
    array (
      'required' => 'The Name vn field is required',
    ),
    'pavement_layer_name_en' => 
    array (
      'required' => 'The Material type field is required',
    ),
    'pavement_layer_name_vn' => 
    array (
      'required' => 'The Material type field is required',
    ),
    'budget_constraint' => 
    array (
      'required' => 'The Budget contraint field is required',
      'numeric' => 'The Budget contraint must be number',
    ),
    'target_risk_level' => 
    array (
      'required' => 'The Target Risk Level field is required',
      'numeric' => 'The Target Risk Level must be number',
      'min' => 'The Target Risk Level between 0 to 100',
    ),
    'sb' => 
    array (
      'required' => 'The SB field is required',
    ),
    'route' => 
    array (
      'required' => 'The Route field is required',
    ),
    'segment' => 
    array (
      'required' => 'The Segment field is required',
    ),
    'date_collection' => 
    array (
      'required' => 'The Date Collection field is required',
    ),
    'km_from' => 
    array (
      'required' => 'The From km field is required',
      'min' => 'The From km must be at least 0',
      'integer' => 'The From km must be an integer type',
    ),
    'm_from' => 
    array (
      'required' => 'The From m field is required',
      'min' => 'The From m must be at least 0',
      'integer' => 'The From m must be an integer type',
    ),
    'km_to' => 
    array (
      'required' => 'The To km  field is required',
      'min' => 'The To km must be at least 0',
      'integer' => 'The To km must be an integer type',
    ),
    'm_to' => 
    array (
      'required' => 'The To m field is required',
      'min' => 'The To m must be at least 0',
      'integer' => 'The To m must be an integer type',
    ),
    'lane_no' => 
    array (
      'required' => 'The No Lane field is required',
    ),
    'name' => 
    array (
      'required' => 'The Name field is required',
      'without_spaces' => 'The Name field contain whitespace',
    ),
    'email' => 
    array (
      'required' => 'The Email field is required',
    ),
    'password' => 
    array (
      'required' => 'The Password field is required',
    ),
    'confirmPassword' => 
    array (
      'required' => 'The Confirm password field is required',
      'same' => 'The Confirm password invalid',
    ),
    'remark' => 
    array (
      'required' => 'The Remark field is required',
    ),
    'survey_time' => 
    array (
      'required' => 'The Date Collection field is required',
      'before' => 'The Date Collection must be a date before now',
      'date' => 'The Date Collection invalid format',
    ),
    'completion_date' => 
    array (
      'before' => 'The Completion Date must be a date before now',
      'required' => 'The Completion Date field is required',
    ),
    'actual_length' => 
    array (
      'required' => 'The Actual Length field is required',
      'min' => 'The Actual Length invalid',
    ),
    'total_width_repair_lane' => 
    array (
      'required' => 'The Total width repair lane field is required',
      'min' => 'The Repair Width invalid',
    ),
    'repair_duration' => 
    array (
      'required' => 'The Repair Duration field is required',
      'min' => 'The Repair Duration invalid',
    ),
    'distance' => 
    array (
      'required' => 'The Distance field is required',
      'min' => 'The Distance (m) must be at least 0',
    ),
    'km_station' => 
    array (
      'required' => 'The Km Station field is required',
      'min' => 'The Km Station must be at least 0',
      'integer' => 'The Km Station must be an integer type',
    ),
    'service_start_year' => 
    array (
      'required' => 'The Service Start Year field is required',
      'before' => 'The Service Start Year must be a date before now',
      'date_format' => 'The Service Start Year invalid format',
    ),
    'temperature' => 
    array (
      'required' => 'The Temperature field is required',
    ),
    'lane_width' => 
    array (
      'required' => 'The Lane width field is required',
      'min' => 'The Lane Width invalid',
    ),
    'm_station' => 
    array (
      'required' => 'The M Station field is required',
      'min' => 'The M Station must be at least 0',
      'integer' => 'The M Station must be an integer type',
    ),
    'car_jeep_up' => 
    array (
      'min' => 'The Car, Jeep (Up) must be at least 0',
    ),
    'car_jeep_down' => 
    array (
      'min' => 'The Car, Jeep (Down) must be at least 0',
    ),
    'light_truck_up' => 
    array (
      'min' => 'The Light Truck (Up) must be at least 0',
    ),
    'medium_truck_up' => 
    array (
      'min' => 'The Medium Truck ( 2 Axles) (Up) must be at least 0',
    ),
    'heavy_truck_up' => 
    array (
      'min' => 'The Heavy Truck ( 3 Axles) (Up) must be at least 0',
    ),
    'heavy_truck3_up' => 
    array (
      'min' => 'The Heavy Truck ( >3 Axles) (Up) must be at least 0',
    ),
    'small_bus_up' => 
    array (
      'min' => 'The Small Bus (Up) must be at least 0',
    ),
    'large_bus_up' => 
    array (
      'min' => 'The Large Bus (Up) must be at least 0',
    ),
    'tractor_up' => 
    array (
      'min' => 'The Tractor (Up) must be at least 0',
    ),
    'motobike_including_3_wheeler_up' => 
    array (
      'min' => 'The Motobike including 3 wheeler (Up) must be at least 0',
    ),
    'bicycle_pedicab_up' => 
    array (
      'min' => 'The Bicycle/Pedicab (Up) must be at least 0',
    ),
    'light_truck_down' => 
    array (
      'min' => 'The Light Truck (Down) must be at least 0',
    ),
    'medium_truck_down' => 
    array (
      'min' => 'The Medium Truck ( 2 Axles) (Down) must be at least 0',
    ),
    'heavy_truck_down' => 
    array (
      'min' => 'The Heavy Truck ( 3 Axles) (Down) must be at least 0',
    ),
    'heavy_truck3_down' => 
    array (
      'min' => 'The Heavy Truck ( >3 Axles) (Down) must be at least 0',
    ),
    'small_bus_down' => 
    array (
      'min' => 'The Small Bus (Down) must be at least 0',
    ),
    'large_bus_down' => 
    array (
      'min' => 'The Large Bus (Down) must be at least 0',
    ),
    'tractor_down' => 
    array (
      'min' => 'The Tractor (Down) must be at least 0',
    ),
    'motobike_including_3_wheeler_down' => 
    array (
      'min' => 'The Motobike including 3 wheeler (Down) must be at least 0',
    ),
    'bicycle_pedicab_down' => 
    array (
      'min' => 'The Bicycle/Pedicab (Down) must be at least 0',
    ),
    'from_lat' => 
    array (
      'regex' => 'The Latitude invalid',
    ),
    'from_lng' => 
    array (
      'regex' => 'The Longitude invalid',
    ),
    'construct_year' => 
    array (
      'required' => 'The Construct Year field is required',
      'before' => 'The Construct Year must be a date before now',
      'date_format' => 'The Construct Year invalid format',
    ),
    'no_lane' => 
    array (
      'required' => 'The No Lane field is required',
      'min' => 'The No Lane invalid',
    ),
    'lane_pos_number' => 
    array (
      'min' => 'The Lane Position number must be at least 0',
    ),
    'annual_precipitation' => 
    array (
      'required' => 'The Annual Precipitation field is required',
    ),
    'repair_method_id' => 
    array (
      'required' => 'The Repair method field is required',
    ),
  ),
  'km_from_is_required' => 'The From km field is requried',
  'km_from_must_be_number' => 'The From km must be number',
  'm_from_is_required' => 'The From m field is required',
  'm_from_must_be_number' => 'The From m must be number',
  'km_to_is_required' => 'The To km field is required',
  'km_to_must_be_number' => 'The To km must be number',
  'm_to_is_required' => 'The To m field is required',
  'm_to_must_be_number' => 'The To m must be number',
  'password_required' => 'The Password field is required',
  'password_min' => 'The Password must be at least 6 characters',
  'password_confirmation_required' => 'The Confirm password field is required',
  'budget_constraint_empty' => 'Budget constraint not empty',
  'budget_constraint_valid_format' => 'Budget constraint invalid format',
  'target_risk_level_empty' => 'Target risk level not empty',
  'target_risk_level_valid_format' => 'Target risk level invalid format',
  'data_valid_beetween' => 'Km invalid',
  'data_required' => 'Input point to split not empty',
  'data_valid_format' => 'Input point to split  invalid format',
  'data_max_string' => 'M invalid',
  'name_vi_required' => 'The Name vi field is required',
  'name_vi_valid_format' => 'The Name vi field invalid format',
  'name_en_required' => 'The Name en field is required',
  'name_en_valid_format' => 'The Name en field invalid format',
  'date_effected_required' => 'Date has effected in actually not empty',
  'date_effected_valid_format' => 'Date has effected in actually invalid format',
  'please_choose_segemnt' => 'Please choose Segment',
  'please_choose_2_segemnt' => 'Please choose must be at least 2 Segment',
  'export_to_admin_db_success' => 'Export success',
  'name_repair_matrix_required' => 'The Name repair matrix field is required',
  'user_not_role' => 'User doesn\'t have role',
  'please_choose_2_RMB' => 'Please choose must be at least 2 RMB',
  'repair_method_required' => 'The Repair method field is required',
  'road_type_required' => 'The Road category field is required',
  'road_class_required' => 'The Road class field is required',
  'surface_required' => 'The Pavement Type field is required',
  'rut_required' => 'The Rutting depth field is required',
  'crack_required' => 'The Crack Ratio field is required',
  'recheck_data_merge' => 'Recheck data merge',
  'success' => 'Success',
  'Are you sure?' => 'Are you sure?',
  'error' => 'Please recheck data!',
  'completion_date_before' => 'The Completion Date must be a date before now',
  'survey_time_must_bigger' => 'The Date Collection must be greater than Date Collection of History record',
  'validation_error' => 'Please recheck data',
  'restore_sucess' => 'Restore success',
  'wp_has_been_planned_delete_fail' => 'Can not delete work planning which has been planned',
  'delete_wp_success' => 'Delete work planning successfully',
  'import_success' => 'Import success',
  'count_new' => 'New sections',
  'new_history' => 'Create new survey ',
  'update_history' => 'Update history',
  'has_err' => 'Have',
  'count_err' => 'Wrong record,',
);
