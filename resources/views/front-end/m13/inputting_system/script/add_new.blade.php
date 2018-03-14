<script type="text/javascript">
	$(document).ready(function () {
		$('#add-new').click(function (e) {
			e.preventDefault();
			var inputData = $("select[name=select_input]").val();
			switch (inputData) {
				case '0':
                    showRI();
					break;
				case '1':
					showMH();
			        break;
				case '2':
					showTV();
					break;
				default:
			}
		});

        $(".modal-content").draggable({
            handle: ".ui-dialog-titlebar",
            // containment: "window"
        });

		$('#add-new-ri-survey').click(function (e) {
            // console.log(angular.element('#widget-grid').scope().errors);
            // angular.element('#widget-grid').scope().errors = [];
			e.preventDefault();
			showRIS();
            $('#model-add-ri').hide();
		});

		$('#add-new-mh-survey').click(function (e) {
			e.preventDefault();
			showMHS();
            $('#model-add-mh').hide();
		});

		$('#add-new-tv-survey').click(function (e) {
			e.preventDefault();
			showTVS();
            $('#model-add-tv').hide();
		});
	});

    //get now date for default data
    function formatDateRIMH() {
        var d = new Date(),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [day, month, year].join('-');
    }

    function formatDateTV() {
        var d = new Date(),
            month = '' + (d.getMonth() + 1),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;

        return [month,year].join('/');
    }

	isapp.directive('clickBtn', function ($parse, $timeout) {
	    return {
			restrict: 'A',
			compile: function($element, attr) {
				var fn = $parse(attr.clickBtn);
				return function clickHandler(scope, element, attrs) {
					element.on('click', function(event) {
						attrs.$set('disabled', true);
						scope.$apply(function() {
							fn(scope, { $event: event }).finally(function() {
								attrs.$set('disabled', false);
                                $timeout(function () {
                                    // console.log($('.red-panel:first').html());
                                    $('.modal-content:visible').find('.red-panel').scrollintoview();
                                    // $('.red-panel').scrollintoview();
                                    // $('.ui-dialog-content').scrollTo('.red-panel:first', 150);
                                });
							});
						});

					});
				};
			}
		};
	});

	isapp.directive('clickBtnDel', function ($parse) {
	    return {
			restrict: 'A',
			compile: function($element, attr) {
				var fn = $parse(attr.clickBtnDel);
				return function (scope, element, attrs) {
					element.on('click', function(event) {
						var check = confirm("{{ trans('validation.Are you sure?') }}");
						if (check) {
							attrs.$set('disabled', true);
							scope.$apply(function() {
								fn(scope, { $event: event }).finally(function() {
									attrs.$set('disabled', false);
								});
							});
						}
					});
				};
			}
		};
	});

	isapp.directive('changeSelect', function ($parse) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                element.on('change', function(event) {
                    var value = element.val().replace("number:", "");
                    if (value != '') {
                        var load = attrs.changeSelect + '(' + value + ')';
                        scope.$apply(function() {
                            $parse(load)(scope);
                        });
                    }
                });
            }
        };
    });

	isapp.controller('AddNewController', function($scope, $http, $q) {
        $scope.Math = window.Math;
        $scope.isNumber = angular.isNumber;
        var categoryMH = [];

        $scope.updateActualLengthRI = function() {
            $scope.formAddRI.actual_length = Math.round($scope.formAddRI.km_to *1000 + $scope.formAddRI.m_to - $scope.formAddRI.km_from*1000 - $scope.formAddRI.m_from + 0);
        }

        $scope.updateActualLengthRIS = function() {
            $scope.formAddRIS.actual_length = Math.round($scope.formAddRIS.km_to *1000 + $scope.formAddRIS.m_to - $scope.formAddRIS.km_from*1000 - $scope.formAddRIS.m_from + 0);
        }

        $scope.updateActualLengthMH = function() {
            $scope.formAddMH.actual_length = Math.round($scope.formAddMH.km_to *1000 + $scope.formAddMH.m_to - $scope.formAddMH.km_from*1000 - $scope.formAddMH.m_from + 0);
        }

        $scope.updateActualLengthMHS = function() {
            $scope.formAddMHS.actual_length = Math.round($scope.formAddMHS.km_to *1000 + $scope.formAddMHS.m_to - $scope.formAddMHS.km_from*1000 - $scope.formAddMHS.m_from + 0);
        }

        $scope.changeDirectionRI = function() {
            if ($scope.formAddRI.direction == 3) {
                $scope.formAddRI.lane_pos_number = 0;
                $scope.formAddRI.no_lane = 1;
            }
            else
            {
                delete $scope.formAddRI.lane_pos_number;
                delete $scope.formAddRI.no_lane;
            }
        }

        $scope.changeDirectionRIS = function() {
            if ($scope.formAddRIS.direction == 3) {
                $scope.formAddRIS.lane_pos_number = 0;
                $scope.formAddRIS.no_lane = 1;
            }
            else
            {
                delete $scope.formAddRIS.lane_pos_number;
                delete $scope.formAddRIS.no_lane;
            }
        }

        $scope.changeDirectionMH = function() {
            if ($scope.formAddMH.direction == 3) {
                $scope.formAddMH.lane_pos_number = 0;
            }
            else
            {
                delete $scope.formAddMH.lane_pos_number;
                delete $scope.formAddMH.no_lane;
            }
        }

        $scope.changeDirectionMHS = function() {
            if ($scope.formAddMHS.direction == 3) {
                $scope.formAddMHS.lane_pos_number = 0;
            }
            else
            {
                delete $scope.formAddMHS.lane_pos_number;
                delete $scope.formAddMHS.no_lane;
            }
        }

        $scope.loadDesignSpeedRI = function() {
            $http({
                method: 'GET',
                url: 'ajax/frontend/terrain/'+ $scope.formAddRI.terrian_type_id +'/road_class/' + $scope.formAddRI.road_class_id,
            }).then(function (response) {
                $scope.formAddRI.design_speed = response.data;
            }, function (xhr) {});
        }

        $scope.loadDesignSpeedRIS = function() {
            $http({
                method: 'GET',
                url: 'ajax/frontend/terrain/'+ $scope.formAddRIS.terrian_type_id +'/road_class/' + $scope.formAddRIS.road_class_id,
            }).then(function (response) {
                $scope.formAddRIS.design_speed = response.data;
            }, function (xhr) {});
        }

        $scope.loadClassification = function(id) {
            $http({
                method: 'GET',
                url: 'ajax/frontend/repair_method/'+ id +'/classification'
            }).then(function (response) {
                console.log(response.data.id);
                $scope.formAddMH.r_classification_id = response.data;
            }, function (xhr) {});
        }
        $scope.loadClassificationMHS = function(id) {
            $http({
                method: 'GET',
                url: 'ajax/frontend/repair_method/'+ id +'/classification'
            }).then(function (response) {
                $scope.formAddMHS.r_classification_id = response.data;
            }, function (xhr) {});
        }

		$scope.loadD = function (id) {
			return $http({
                method: 'GET',
                url: 'ajax/frontend/province/' + id + '/district',
            });
		}

		$scope.loadW = function (id) {
			return $http({
                method: 'GET',
                url: 'ajax/frontend/district/' + id + '/ward',
            })
		}

		//RI
		$scope.loadDistrictFromRI = function(id) {
            $scope.loadD(id).then(function (response) {
                angular.element('#select2-district_from_RI-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtFromRI = response.data;
                angular.element('#select2-ward_from_id_RI-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardFromRI = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardFromRI = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_from_id_RI-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardFromRI = response.data;
            }, function (response) {

            });
        }

        $scope.loadDistrictToRI = function(id) {
            $scope.loadD(id).then(function (response) {
            	angular.element('#select2-district_to_RI-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtToRI = response.data;
                angular.element('#select2-ward_to_id_RI-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardToRI = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardToRI = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_to_id_RI-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardToRI = response.data;
            }, function (response) {

            });
        }

        //MH
        $scope.loadDistrictFromMH = function(id) {
            $scope.loadD(id).then(function (response) {
                angular.element('#select2-district_from_MH-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtFromMH = response.data;
                angular.element('#select2-ward_from_id_MH-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardFromMH = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardFromMH = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_from_id_MH-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardFromMH = response.data;
            }, function (response) {

            });
        }

        $scope.loadDistrictToMH = function(id) {
            $scope.loadD(id).then(function (response) {
            	angular.element('#select2-district_to_MH-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtToMH = response.data;
                angular.element('#select2-ward_to_id_MH-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardToMH = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardToMH = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_to_id_MH-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardToMH = response.data;
            }, function (response) {

            });
        }

        //RIS
		$scope.loadDistrictFromRIS = function(id) {
            $scope.loadD(id).then(function (response) {
                angular.element('#select2-district_from_RIS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtFromRIS = response.data;
                angular.element('#select2-ward_from_id_RIS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardFromRIS = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardFromRIS = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_from_id_RIS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardFromRIS = response.data;
            }, function (response) {

            });
        }

        $scope.loadDistrictToRIS = function(id) {
            $scope.loadD(id).then(function (response) {
            	angular.element('#select2-district_to_RIS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtToRIS = response.data;
                angular.element('#select2-ward_to_id_RIS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardToRIS = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardToRIS = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_to_id_RIS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardToRIS = response.data;
            }, function (response) {

            });
        }

        //MHS
        $scope.loadDistrictFromMHS = function(id) {
            $scope.loadD(id).then(function (response) {
                angular.element('#select2-district_from_MHS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtFromMHS = response.data;
                angular.element('#select2-ward_from_id_MHS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardFromMHS = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardFromMHS = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_from_id_MHS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardFromMHS = response.data;
            }, function (response) {

            });
        }

        $scope.loadDistrictToMHS = function(id) {
            $scope.loadD(id).then(function (response) {
            	angular.element('#select2-district_to_MHS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtToMHS = response.data;
                angular.element('#select2-ward_to_id_MHS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardToMHS = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardToMHS = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_to_id_MHS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardToMHS = response.data;
            }, function (response) {

            });
        }

        //TV
		$scope.loadDistrictTV = function(id) {
            $scope.loadD(id).then(function (response) {
                angular.element('#select2-district_TV-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtTV = response.data;
                angular.element('#select2-ward_id_TV-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardTV = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardTV = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_id_TV-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardTV = response.data;
            }, function (response) {

            });
        }

        //TVS
		$scope.loadDistrictTVS = function(id) {
            $scope.loadD(id).then(function (response) {
                angular.element('#select2-district_TVS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.districtTVS = response.data;
                angular.element('#select2-ward_id_TVS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardTVS = {};
            }, function (xhr) {

            });
        }

        $scope.loadWardTVS = function(id) {
            $scope.loadW(id).then(function (response) {
            	angular.element('#select2-ward_id_TVS-container').html('{{ trans("back_end.please_choose")  }}');
                $scope.wardTVS = response.data;
            }, function (response) {

            });
        }

        $scope.loadSurfaceMH = function (id) {
            $http({
                method: 'GET',
                url: 'ajax/frontend/material_type/'+ id +'/surface'
            }).then(function (response) {
                $scope.formAddMH.surface_id = response.data.surface_id;
                $scope.categoryMH = response.data.repair_categories;
                if (response.data.repair_categories) {
                    $scope.formAddMH.r_category_id = response.data.repair_categories[0].id;
                }
            }, function (xhr) {});
        }

        $scope.loadSurfaceMHS = function (id) {
            $http({
                method: 'GET',
                url: 'ajax/frontend/material_type/'+ id +'/surface'
            }).then(function (response) {
                $scope.formAddMHS.surface_id = response.data.surface_id;
                $scope.categoryMHS = response.data.repair_categories;
                if (response.data.repair_categories) {
                    $scope.formAddMHS.r_category_id = response.data.repair_categories[0].id;
                }
            }, function (xhr) {});
        }

        $scope.loadSurfaceRI = function (id) {
            $http({
                method: 'GET',
                url: 'ajax/frontend/material_type/'+ id +'/surface'
            }).then(function (response) {
                $scope.formAddRI.surface_id = response.data.surface_id;
            }, function (xhr) {});
        }

        $scope.loadSurfaceRIS = function (id) {
            $http({
                method: 'GET',
                url: 'ajax/frontend/material_type/'+ id +'/surface'
            }).then(function (response) {
                $scope.formAddRIS.surface_id = response.data.surface_id;
            }, function (xhr) {});
        }

        $scope.loadRepairCategoryMHS = function(id) {
            $http({
                method: 'GET',
                url: 'ajax/frontend/surface/'+ id +'/repair_category'
            }).then(function (response) {
                $scope.categoryMHS = response.data;
                if (response.data) {
                    $scope.formAddMHS.r_category_id = response.data[0].id;
                }
            }, function (xhr) {});
        }

        // $http({
        //     method: 'GET',
        //     url: 'ajax/frontend/surface/'+ "{{ $default_data['surface_id'] }}" +'/repair_category'
        // }).then(function (response) {
        //     $scope.categoryMH = response.data;
        //     $scope.categoryMHS = response.data;
        //     categoryMH = response.data;
        //     categoryMHS = response.data;
        //     $scope.formAddMH.r_category_id = response.data[0].id;
        //     $scope.formAddMHS.r_category_id = response.data[0].id;
        // }, function (xhr) {});


		// value default section
		var data_ri_default = {
			road_class_id: "{{ $default_data['road_class_id'] }}",
			direction: '1',
			terrian_type_id: "{{ $default_data['terrian_type_id'] }}",
            design_speed: 'N/A (km/h)',
            survey_time : formatDateRIMH()
		};
		var data_mh_default = {
            repair_method_id: "{{ $default_data['repair_method_id'] }}",
			direction: '1',
			direction_running: '0',
			r_category_id: parseInt("{{ $default_data['r_category_id'] }}"),
			r_struct_type_id: "{{ $default_data['r_struct_type_id'] }}",
            r_classification_id: "{{ $default_data['r_classification_id'] }}",
			// surface_id: "{{ $default_data['surface_id'] }}",
            survey_time : formatDateRIMH()
		};
		var data_tv_default = {
            survey_time: formatDateTV()
		};

		$scope.formAddMH = angular.copy(data_mh_default);
		$scope.formAddRI = angular.copy(data_ri_default);
		$scope.formAddTV = angular.copy(data_tv_default);
		$scope.formAddMHS = angular.copy(data_mh_default);
		$scope.formAddRIS = angular.copy(data_ri_default);
		$scope.formAddTVS = angular.copy(data_tv_default);

		$scope.cancelRI = function() {
			$scope.errors=[];
        	$scope.formAddRI = angular.copy(data_ri_default);
            hideRI();
        	$scope.districtFromRI = {};
        	$scope.districtToRI = {};
        	$scope.wardFromRI = {};
        	$scope.wardToRI = {};
        	$scope.province_from_RI = '';
        	$scope.province_to_RI = '';
        	angular.element('#select2-province_from_RI-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-province_to_RI-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-district_from_RI-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-ward_from_id_RI-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-district_to_RI-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-ward_to_id_RI-container').html('{{ trans("back_end.please_choose")  }}');
	    };

	    $scope.cancelMH = function() {
        	$scope.errors=[];
        	$scope.formAddMH = angular.copy(data_mh_default);
        	hideMH();
        	$scope.districtFromMH = {};
        	$scope.districtToMH = {};
        	$scope.wardFromMH = {};
        	$scope.wardToMH = {};
        	$scope.province_from_MH = '';
        	$scope.province_to_MH = '';
        	angular.element('#select2-province_from_MH-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-province_to_MH-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-district_from_MH-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-ward_from_id_MH-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-district_to_MH-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-ward_to_id_MH-container').html('{{ trans("back_end.please_choose")  }}');
            $scope.categoryMH = categoryMH;
	    };

	    $scope.cancelTV = function() {
        	$scope.errors=[];
        	$scope.formAddTV = angular.copy(data_tv_default);
        	hideTV();
        	$scope.districtTV = {};
        	$scope.wardTV = {};
        	$scope.province_TV = '';
        	angular.element('#select2-province_TV-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-district_TV-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-ward_id_TV-container').html('{{ trans("back_end.please_choose")  }}');
	    };

	    $scope.cancelRIS = function() {
			$scope.errors=[];
        	$scope.formAddRIS = angular.copy(data_ri_default);
        	hideRIS();
            $scope.cancelRI();
        	$scope.districtFromRIS = {};
        	$scope.districtToRIS = {};
        	$scope.wardFromRIS = {};
        	$scope.wardToRIS= {};
        	$scope.province_from_RIS = '';
        	$scope.province_to_RIS = '';
        	angular.element('#select2-province_from_RIS-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-province_to_RIS-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-district_from_RIS-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-ward_from_id_RIS-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-district_to_RIS-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-ward_to_id_RIS-container').html('{{ trans("back_end.please_choose")  }}');
	    };

	    $scope.cancelMHS = function() {
        	$scope.errors=[];
        	$scope.formAddMHS = angular.copy(data_mh_default);
        	hideMHS();
            $scope.cancelMH();
        	$scope.districtFromMH = {};
        	$scope.districtToMH = {};
        	$scope.wardFromMH = {};
        	$scope.wardToMH = {};
        	$scope.province_from_MHS = '';
        	$scope.province_to_MHS = '';
        	angular.element('#select2-province_from_MHS-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-province_to_MHS-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-district_from_MHS-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-ward_from_id_MHS-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-district_to_MHS-container').html('{{ trans("back_end.please_choose")  }}');
			angular.element('#select2-ward_to_id_MHS-container').html('{{ trans("back_end.please_choose")  }}');
            $scope.categoryMHS = categoryMHS;
	    };

	    $scope.cancelTVS = function() {
        	$scope.errors=[];
        	$scope.formAddTVS = angular.copy(data_tv_default);
            hideTVS();
            $scope.cancelTV();
        	$scope.districtTVS = {};
        	$scope.wardTVS = {};
        	$scope.province_TVS = '';
        	angular.element('#select2-province_TVS-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-district_TVS-container').html('{{ trans("back_end.please_choose")  }}');
        	angular.element('#select2-ward_id_TVS-container').html('{{ trans("back_end.please_choose")  }}');
	    };

        $scope.addMH = function() {
        	var data = angular.merge($scope.formAddMH, scope_manage());
			// var data = Object.assign({}, $scope.formAddMH, scope_manage());
	        return $http({
	            method : "POST",
	            url: 'ajax/frontend/maintenance_history',
	            params: data,
	        }).then(function (response) {
	            $scope.cancelMH();
	            toastr.success("{{ trans('validation.success') }}");
                getDataSegment();
	        }, function myError(xhr) {
	        	if (xhr.status == 422) {
	        		$scope.errors = convertErrorMH(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    };

	    $scope.addRI = function() {
            var data = angular.merge($scope.formAddRI, scope_manage());
	    	return $http({
	            method : "POST",
	            url: 'ajax/frontend/road_inventory',
	            params: data,
	        }).then(function (response) {
	        	$scope.cancelRI();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorRI(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.addTV = function() {
			var total_traffic_volume_up = angular.element('input[name=total_traffic_volume_up]').val();
			var heavy_traffic_up = angular.element('input[name=heavy_traffic_up]').val();
			var total_traffic_volume_down = angular.element('input[name=total_traffic_volume_down]').val();
			var heavy_traffic_down = angular.element('input[name=heavy_traffic_down]').val();
			var data_traffic = {
				total_traffic_volume_up: total_traffic_volume_up,
				heavy_traffic_up: heavy_traffic_up,
				total_traffic_volume_down: total_traffic_volume_down,
				heavy_traffic_down: heavy_traffic_down
			};
            var data = angular.merge($scope.formAddTV, scope_manage(), data_traffic);
	    	return $http({
	            method : "POST",
	            url: 'ajax/frontend/traffic_volume',
	            params: data,
	        }).then(function (response) {
	            $scope.cancelTV();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorTV(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.showRI = function(id) {
	    	$http({
	            method : "GET",
	            url: 'ajax/frontend/road_inventory/' + id
	        }).then(function (response) {
	            $scope.formAddRI = response.data.section;
                if (response.data.section.ward_from_id) {
                    var rd = response.data.data_from;
                    $scope.wardFromRI = rd.list_ward;
                    $scope.districtFromRI = rd.list_district;
                    $scope.province_from_RI = rd.p_id;
                    $scope.district_from_RI = rd.d_id;
                    angular.element('#select2-province_from_RI-container').html(rd.p_name);
                    angular.element('#select2-district_from_RI-container').html(rd.d_name);
                    angular.element('#select2-ward_from_id_RI-container').html(rd.w_name);
                }
                if (response.data.section.ward_to_id) {
                    var rd = response.data.data_to;
                    $scope.wardToRI = rd.list_ward;
                    $scope.districtToRI = rd.list_district;
                    $scope.province_to_RI = rd.p_id;
                    $scope.district_to_RI = rd.d_id;
                    angular.element('#select2-province_to_RI-container').html(rd.p_name);
                    angular.element('#select2-district_to_RI-container').html(rd.d_name);
                    angular.element('#select2-ward_to_id_RI-container').html(rd.w_name);
                }
                showRI();
	        }, function myError(xhr) {
	            console.log(xhr.statusText);
	        });
	    }

	    $scope.showMH = function(id) {
	    	$http({
	            method : "GET",
	            url: 'ajax/frontend/maintenance_history/' + id
	        }).then(function (response) {
	            $scope.formAddMH = response.data.section;
                if (response.data.section.ward_from_id) {
                    var rd = response.data.data_from;
                    $scope.wardFromMH = rd.list_ward;
                    $scope.districtFromMH = rd.list_district;
                    $scope.province_from_MH = rd.p_id;
                    $scope.district_from_MH = rd.d_id;
                    angular.element('#select2-province_from_MH-container').html(rd.p_name);
                    angular.element('#select2-district_from_MH-container').html(rd.d_name);
                    angular.element('#select2-ward_from_id_MH-container').html(rd.w_name);
                }
                if (response.data.section.ward_to_id) {
                    var rd = response.data.data_to;
                    $scope.wardToMH = rd.list_ward;
                    $scope.districtToMH = rd.list_district;
                    $scope.province_to_MH = rd.p_id;
                    $scope.district_to_MH = rd.d_id;
                    angular.element('#select2-province_to_MH-container').html(rd.p_name);
                    angular.element('#select2-district_to_MH-container').html(rd.d_name);
                    angular.element('#select2-ward_to_id_MH-container').html(rd.w_name);
                }
                $scope.categoryMH = response.data.data_classification;
                showMH();
	        }, function myError(xhr) {
	            console.log(xhr.statusText);
	        });
	    }

	    $scope.showTV = function(id) {
	    	$http({
	            method : "GET",
	            url: 'ajax/frontend/traffic_volume/' + id
	        }).then(function (response) {
	            $scope.formAddTV = response.data.section;
                if (response.data.section.ward_id) {
                    var rd = response.data.data_station;
                    $scope.wardTV = rd.list_ward;
                    $scope.districtTV = rd.list_district;
                    $scope.province_TV = rd.p_id;
                    $scope.district_TV = rd.d_id;
                    angular.element('#select2-province_TV-container').html(rd.p_name);
                    angular.element('#select2-district_TV-container').html(rd.d_name);
                    angular.element('#select2-ward_id_TV-container').html(rd.w_name);
                }
                showTV();
	        }, function myError(xhr) {
	            console.log(xhr.statusText);
	        });
	    }

	    $scope.editRI = function() {
            var data = angular.merge($scope.formAddRI, scope_manage());
	    	console.log(data);
	    	return $http({
	            method : "PUT",
	            url: 'ajax/frontend/road_inventory/' + $scope.formAddRI.id,
	            params: data,
	        }).then(function (response) {
	            $scope.cancelRI();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorRI(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.editMH = function() {
            var data = angular.merge($scope.formAddMH, scope_manage());
	    	return $http({
	            method : "PUT",
	            url: 'ajax/frontend/maintenance_history/' + $scope.formAddMH.id,
	            params: data,
	        }).then(function (response) {
	            $scope.cancelMH();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorMH(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.editTV = function() {
			var total_traffic_volume_up = angular.element('input[name=total_traffic_volume_up]').val();
			var heavy_traffic_up = angular.element('input[name=heavy_traffic_up]').val();
			var total_traffic_volume_down = angular.element('input[name=total_traffic_volume_down]').val();
			var heavy_traffic_down = angular.element('input[name=heavy_traffic_down]').val();
			var data_traffic = {
				total_traffic_volume_up: total_traffic_volume_up,
				heavy_traffic_up: heavy_traffic_up,
				total_traffic_volume_down: total_traffic_volume_down,
				heavy_traffic_down: heavy_traffic_down
			};
            var data = angular.merge($scope.formAddTV, scope_manage(), data_traffic);
	    	return $http({
	            method : "PUT",
	            url: 'ajax/frontend/traffic_volume/' + $scope.formAddTV.id,
	            params: data,
	        }).then(function (response) {
	            $scope.cancelTV();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorTV(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.deleteRI = function() {
	    	return $http({
	            method : "DELETE",
	            url: 'ajax/frontend/road_inventory/' + $scope.formAddRI.id
	        }).then(function (response) {
	            $scope.cancelRI();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataSegment();
	        }, function myError(xhr) {
	            toastr.error("{{ trans('validation.error') }}");
	        });
	    }

	    $scope.deleteMH = function() {
	    	return $http({
	            method : "DELETE",
	            url: 'ajax/frontend/maintenance_history/' + $scope.formAddMH.id
	        }).then(function (response) {
	            $scope.cancelMH();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataSegment();
	        }, function myError(xhr) {
	            toastr.error("{{ trans('validation.error') }}");
	        });
	    }

	    $scope.deleteTV = function() {
	    	return $http({
	            method : "DELETE",
	            url: 'ajax/frontend/traffic_volume/' + $scope.formAddTV.id
	        }).then(function (response) {
	            $scope.cancelTV();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataSegment();
	        }, function myError(xhr) {
	            toastr.error("{{ trans('validation.error') }}");
	        });
	    }

	    $scope.addRIS = function() {
            // var data = angular.merge($scope.formAddRIS, {id: $scope.formAddRI.id},scope_manage());
	    	var data = Object.assign({}, $scope.formAddRIS, {id: $scope.formAddRI.id}, scope_manage());
	    	return $http({
	            method : "POST",
	            url: 'ajax/frontend/road_inventory_survey',
	            params: data,
	        }).then(function (response) {
	        	$scope.cancelRIS();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataHistory(scope_manage().segment_id, lane_pos_number, direction);
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorRI(xhr.data);
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.addMHS = function() {
            // var data = angular.merge($scope.formAddMHS, {id: $scope.formAddMH.id},scope_manage());
	    	var data = Object.assign({}, $scope.formAddMHS, {id: $scope.formAddMH.id}, scope_manage());
	    	return $http({
	            method : "POST",
	            url: 'ajax/frontend/maintenance_history_survey',
	            params: data,
	        }).then(function (response) {
	        	$scope.cancelMHS();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataHistory(scope_manage().segment_id, lane_pos_number, direction);
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorMH(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.addTVS = function() {
	    	var total_traffic_volume_up = angular.element('input[name=total_traffic_volume_up_s]').val();
			var heavy_traffic_up = angular.element('input[name=heavy_traffic_up_s]').val();
			var total_traffic_volume_down = angular.element('input[name=total_traffic_volume_down_s]').val();
			var heavy_traffic_down = angular.element('input[name=heavy_traffic_down_s]').val();
			var data_traffic = {
				total_traffic_volume_up: total_traffic_volume_up,
				heavy_traffic_up: heavy_traffic_up,
				total_traffic_volume_down: total_traffic_volume_down,
				heavy_traffic_down: heavy_traffic_down
			};
            // var data = angular.merge($scope.formAddTVS, {id: $scope.formAddTV.id},scope_manage(), data_traffic);
	    	var data = Object.assign({}, $scope.formAddTVS, {id: $scope.formAddTV.id}, scope_manage(), data_traffic);
	    	return $http({
	            method : "POST",
	            url: 'ajax/frontend/traffic_volume_survey',
	            params: data,
	        }).then(function (response) {
	        	$scope.cancelTVS();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataHistory(scope_manage().segment_id, lane_pos_number, direction);
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorTV(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.showRIS = function(id) {
	    	$http({
	            method : "GET",
	            url: 'ajax/frontend/road_inventory_survey/' + id
	        }).then(function (response) {
                $scope.formAddRIS = response.data.section;
                if (response.data.section.ward_from_id) {
                    var rd = response.data.data_from;
                    $scope.wardFromRIS = rd.list_ward;
                    $scope.districtFromRIS = rd.list_district;
                    $scope.province_from_RIS = rd.p_id;
                    $scope.district_from_RIS = rd.d_id;
                    angular.element('#select2-province_from_RIS-container').html(rd.p_name);
                    angular.element('#select2-district_from_RIS-container').html(rd.d_name);
                    angular.element('#select2-ward_from_id_RIS-container').html(rd.w_name);
                }
                if (response.data.section.ward_to_id) {
                    var rd = response.data.data_to;
                    $scope.wardToRIS = rd.list_ward;
                    $scope.districtToRIS = rd.list_district;
                    $scope.province_to_RIS = rd.p_id;
                    $scope.district_to_RIS = rd.d_id;
                    angular.element('#select2-province_to_RIS-container').html(rd.p_name);
                    angular.element('#select2-district_to_RIS-container').html(rd.d_name);
                    angular.element('#select2-ward_to_id_RIS-container').html(rd.w_name);
                }
                showRIS();
	        }, function myError(xhr) {
	            console.log(xhr.statusText);
	        });
	    }

	    $scope.showMHS = function(id) {
	    	$http({
	            method : "GET",
	            url: 'ajax/frontend/maintenance_history_survey/' + id
	        }).then(function (response) {
                $scope.formAddMHS = response.data.section;
                if (response.data.section.ward_from_id) {
                    var rd = response.data.data_from;
                    $scope.wardFromMHS = rd.list_ward;
                    $scope.districtFromMHS = rd.list_district;
                    $scope.province_from_MHS = rd.p_id;
                    $scope.district_from_MHS = rd.d_id;
                    angular.element('#select2-province_from_MHS-container').html(rd.p_name);
                    angular.element('#select2-district_from_MHS-container').html(rd.d_name);
                    angular.element('#select2-ward_from_id_MHS-container').html(rd.w_name);
                }
                if (response.data.section.ward_to_id) {
                    var rd = response.data.data_to;
                    $scope.wardToMHS = rd.list_ward;
                    $scope.districtToMHS = rd.list_district;
                    $scope.province_to_MHS = rd.p_id;
                    $scope.district_to_MHS = rd.d_id;
                    angular.element('#select2-province_to_MHS-container').html(rd.p_name);
                    angular.element('#select2-district_to_MHS-container').html(rd.d_name);
                    angular.element('#select2-ward_to_id_MHS-container').html(rd.w_name);
                }
                $scope.categoryMHS = response.data.data_classification;
                showMHS();
	        }, function myError(xhr) {
	            console.log(xhr.statusText);
	        });
	    }

	    $scope.showTVS = function(id) {
	    	$http({
	            method : "GET",
	            url: 'ajax/frontend/traffic_volume_survey/' + id
	        }).then(function (response) {
                $scope.formAddTVS = response.data.section;
                if (response.data.section.ward_id) {
                    var rd = response.data.data_station;
                    $scope.wardTVS = rd.list_ward;
                    $scope.districtTVS = rd.list_district;
                    $scope.province_TVS = rd.p_id;
                    $scope.district_TVS = rd.d_id;
                    angular.element('#select2-province_TVS-container').html(rd.p_name);
                    angular.element('#select2-district_TVS-container').html(rd.d_name);
                    angular.element('#select2-ward_id_TVS-container').html(rd.w_name);
                }
                showTVS();
	        }, function myError(xhr) {
	            console.log(xhr.statusText);
	        });
	    }

	    $scope.editRIS = function() {
            var data = angular.merge($scope.formAddRIS, scope_manage());
	    	return $http({
	            method : "PUT",
	            url: 'ajax/frontend/road_inventory_survey/' + $scope.formAddRIS.id,
	            params: data,
	        }).then(function (response) {
	        	$scope.cancelRIS();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataHistory(scope_manage().segment_id, lane_pos_number, direction);
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorRI(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.editMHS = function() {
            var data = angular.merge($scope.formAddMHS, scope_manage());
	    	return $http({
	            method : "PUT",
	            url: 'ajax/frontend/maintenance_history_survey/' + $scope.formAddMHS.id,
	            params: data,
	        }).then(function (response) {
	        	$scope.cancelMHS();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataHistory(scope_manage().segment_id, lane_pos_number, direction);
                getDataSegment();
	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorMH(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.editTVS = function() {
			var total_traffic_volume_up = angular.element('input[name=total_traffic_volume_up_s]').val();
			var heavy_traffic_up = angular.element('input[name=heavy_traffic_up_s]').val();
			var total_traffic_volume_down = angular.element('input[name=total_traffic_volume_down_s]').val();
			var heavy_traffic_down = angular.element('input[name=heavy_traffic_down_s]').val();
			var data_traffic = {
				total_traffic_volume_up: total_traffic_volume_up,
				heavy_traffic_up: heavy_traffic_up,
				total_traffic_volume_down: total_traffic_volume_down,
				heavy_traffic_down: heavy_traffic_down
			};
            var data = angular.merge($scope.formAddTVS, scope_manage(), data_traffic);
	    	return $http({
	            method : "PUT",
	            url: 'ajax/frontend/traffic_volume_survey/' + $scope.formAddTVS.id,
	            params: data,
	        }).then(function (response) {
	            $scope.cancelTVS();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataHistory(scope_manage().segment_id, lane_pos_number, direction);
                getDataSegment();

	        }, function myError(xhr) {
	            if (xhr.status == 422) {
	        		$scope.errors = convertErrorTV(xhr.data);
                    toastr.error("{{ trans('validation.validation_error') }}");
	        	} else {
	        		toastr.error("{{ trans('validation.error') }}");
	        	}
	        });
	    }

	    $scope.deleteRIS = function() {
	    	return $http({
	            method : "DELETE",
	            url: 'ajax/frontend/road_inventory_survey/' + $scope.formAddRIS.id
	        }).then(function (response) {
	            $scope.cancelRIS();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataHistory(scope_manage().segment_id, lane_pos_number, direction);
                getDataSegment();
	        }, function myError(xhr) {
	            toastr.error("{{ trans('validation.error') }}");
	        });
	    }

	    $scope.deleteMHS = function() {
	    	return $http({
	            method : "DELETE",
	            url: 'ajax/frontend/maintenance_history_survey/' + $scope.formAddMHS.id
	        }).then(function (response) {
	            $scope.cancelMHS();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataHistory(scope_manage().segment_id, lane_pos_number, direction);
                getDataSegment();
	        }, function myError(xhr) {
	            toastr.error("{{ trans('validation.error') }}");
	        });
	    }

	    $scope.deleteTVS = function() {
	    	return $http({
	            method : "DELETE",
	            url: 'ajax/frontend/traffic_volume_survey/' + $scope.formAddTVS.id
	        }).then(function (response) {
	            $scope.cancelTVS();
	            toastr.success("{{ trans('validation.success')  }}");
                getDataHistory(scope_manage().segment_id, lane_pos_number, direction);
                getDataSegment();
	        }, function myError(xhr) {
	            toastr.error("{{ trans('validation.error') }}");
	        });
	    }

        $scope.copyRI = function(id) {
            $http({
                method : "GET",
                url: 'ajax/frontend/road_inventory/' + id
            }).then(function (response) {
                $scope.formAddRI = response.data.section;
                delete $scope.formAddRI['id'];
                if (response.data.section.ward_from_id) {
                    var rd = response.data.data_from;
                    $scope.wardFromRI = rd.list_ward;
                    $scope.districtFromRI = rd.list_district;
                    $scope.province_from_RI = rd.p_id;
                    $scope.district_from_RI = rd.d_id;
                    angular.element('#select2-province_from_RI-container').html(rd.p_name);
                    angular.element('#select2-district_from_RI-container').html(rd.d_name);
                    angular.element('#select2-ward_from_id_RI-container').html(rd.w_name);
                }
                if (response.data.section.ward_to_id) {
                    var rd = response.data.data_to;
                    $scope.wardToRI = rd.list_ward;
                    $scope.districtToRI = rd.list_district;
                    $scope.province_to_RI = rd.p_id;
                    $scope.district_to_RI = rd.d_id;
                    angular.element('#select2-province_to_RI-container').html(rd.p_name);
                    angular.element('#select2-district_to_RI-container').html(rd.d_name);
                    angular.element('#select2-ward_to_id_RI-container').html(rd.w_name);
                }
                showRI();
            }, function myError(xhr) {
                console.log(xhr.statusText);
            });
        }

        $scope.copyMH = function(id) {
            $http({
                method : "GET",
                url: 'ajax/frontend/maintenance_history/' + id
            }).then(function (response) {
                $scope.formAddMH = response.data.section;
                delete $scope.formAddMH['id'];
                if (response.data.section.ward_from_id) {
                    var rd = response.data.data_from;
                    $scope.wardFromMH = rd.list_ward;
                    $scope.districtFromMH = rd.list_district;
                    $scope.province_from_MH = rd.p_id;
                    $scope.district_from_MH = rd.d_id;
                    angular.element('#select2-province_from_MH-container').html(rd.p_name);
                    angular.element('#select2-district_from_MH-container').html(rd.d_name);
                    angular.element('#select2-ward_from_id_MH-container').html(rd.w_name);
                }
                if (response.data.section.ward_to_id) {
                    var rd = response.data.data_to;
                    $scope.wardToMH = rd.list_ward;
                    $scope.districtToMH = rd.list_district;
                    $scope.province_to_MH = rd.p_id;
                    $scope.district_to_MH = rd.d_id;
                    angular.element('#select2-province_to_MH-container').html(rd.p_name);
                    angular.element('#select2-district_to_MH-container').html(rd.d_name);
                    angular.element('#select2-ward_to_id_MH-container').html(rd.w_name);
                }
                $scope.categoryMH = response.data.data_classification;
                showMH();
            }, function myError(xhr) {
                console.log(xhr.statusText);
            });
        }

        $scope.copyTV = function(id) {
            $http({
                method : "GET",
                url: 'ajax/frontend/traffic_volume/' + id
            }).then(function (response) {
                $scope.formAddTV = response.data.section;
                delete $scope.formAddTV['id'];
                if (response.data.section.ward_id) {
                    var rd = response.data.data_station;
                    $scope.wardTV = rd.list_ward;
                    $scope.districtTV = rd.list_district;
                    $scope.province_TV = rd.p_id;
                    $scope.district_TV = rd.d_id;
                    angular.element('#select2-province_TV-container').html(rd.p_name);
                    angular.element('#select2-district_TV-container').html(rd.d_name);
                    angular.element('#select2-ward_id_TV-container').html(rd.w_name);
                }
                showTV();
            }, function myError(xhr) {
                console.log(xhr.statusText);
            });
        }
    });


	function scope_manage() {
		var scope_manage = {
			rmb: rmb_select.val(), 
			sb: sb_select.val(), 
			route: route_select.val(), 
			segment_id: segment_select.val()
		};
		return scope_manage;
	}

	function showModalMH(id) {
		var $scope = angular.element('#widget-grid').scope().showMH(id);
	}

	function showModalRI(id) {
		var $scope = angular.element('#widget-grid').scope().showRI(id);
	}

	function showModalTV(id) {
		var $scope = angular.element('#widget-grid').scope().showTV(id);
	}

	function showModalRIS(id) {
		var $scope = angular.element('#widget-grid').scope().showRIS(id);
	}

	function showModalMHS(id) {
		var $scope = angular.element('#widget-grid').scope().showMHS(id);
	}

	function showModalTVS(id) {
		var $scope = angular.element('#widget-grid').scope().showTVS(id);
	}

    function convertErrorRI(errors) {
        var dataset = {
            general_error: [
                'survey_time', 'terrian_type_id', 'road_class_id'
            ],
            chainage_error: [
                'km_from', 'm_from', 'km_to', 'm_to', 'from_lat', 'from_lng', 'to_lat', 'to_lng'
            ],
            motorized_lane_error: [
                'direction', 'lane_pos_number', 'no_lane', 'lane_width'
            ],
            other_information_error: [
                'construct_year', 'service_start_year', 'temperature', 'annual_precipitation', 'actual_length'
            ],
            material_layer_error: [
                'data[6][material_type]', 'data[6][thickness]'
            ]
        };

        for (var i in dataset) {
            var inputs = dataset[i];
            for (var j in inputs) {
                if (typeof errors[inputs[j]] != 'undefined') {
                    errors[i] = true;
                    break;
                }
            }
        }

        return errors;
    }

    function convertErrorMH(errors) {
        var dataset = {
            general_error: [
                'survey_time', 'completion_date', 'repair_duration'
            ],
            chainage_error: [
                'km_from', 'm_from', 'km_to', 'm_to', 'from_lat', 'from_lng', 'to_lat', 'to_lng'
            ],
            repair_section_error: [
                'direction', 'lane_pos_number', 'actual_length', 'total_width_repair_lane'
            ],
            position_error: [
                'direction_running', 'distance'
            ],
            repair_method_error: [
                'repair_method_id', 'r_classification_id', 'r_struct_type_id'
            ],
            material_layer_error: [
                'data[6][material_type]', 'data[6][thickness]'
            ]
        };

        for (var i in dataset) {
            var inputs = dataset[i];
            for (var j in inputs) {
                if (typeof errors[inputs[j]] != 'undefined') {
                    errors[i] = true;
                    break;
                }
            }
        }

        return errors;
    }

    function convertErrorTV(errors) {
        var dataset = {
            information_error: [
                'survey_time', 'name_en', 'name_vn', 'km_station', 'm_station', 'lat_station', 'lng_station'
            ]
        };

        for (var i in dataset) {
            var inputs = dataset[i];
            for (var j in inputs) {
                if (typeof errors[inputs[j]] != 'undefined') {
                    errors[i] = true;
                    break;
                }
            }
        }

        return errors;
    }

    function copyInputData(type, id) {
        switch (type) {
            case 'ri':
                angular.element('#widget-grid').scope().copyRI(id);
                break;
            case 'mh':
                showMH();
                break;
            case 'tv':
                showTV();
                break;
            default:
        }
    }
</script>
