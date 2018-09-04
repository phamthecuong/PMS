<template id="scope-manager">
	<article  class="col-lg-9 col-md-8">
	    @box_open(trans('back_end.scope_manage'))
	    <div class="widget-body">
	        {!! Form::open(["url" => "", "method" => "post"]) !!}
	        <div class="row">
	            <div class="col-xs-6">
	            	<div class="form-group ">
	            		<label for="organization" class="control-label">Road Management Bureau </label> 
            			<select id="rmb_id" 
	            				name="organization" 
	            				class="form-control" 
	            				v-model="rmb_select"
		            			v-on:change="loadSB()">
	            			@php
	            				$data = [];
	            				$data = \App\Models\tblOrganization::getListRmb();
	            			@endphp
	            			
	            			@foreach($data as $val) 
	            				<option value="{{$val['value']}}">{{$val['name']}}</option> 
	            			@endforeach
	            		</select>
	            	</div>
	            
	                <div class="form-group">
	                	<label for="route_branch" class="control-label">Route Name</label> 
	                	<select id="road_name" 
	                			name="route_branch" 
	                			class="form-control"
	                			v-model="route_select"
	                			v-on:change="loadSegment()">
	                		<option v-for="data in routeData" v-bind:value="data.id">
	                			@{{data.route_name}}-@{{data.branch_number}}
	                		</option>
	                	</select>
	                </div>
	            </div>
	            <div class="col-xs-6">
	                <div class="form-group">
	                    <label for="sub_organization" class="control-label">Sub Bureau</label>
	                    <select class="form-control" 
	                    		id="sb_id" 
	                    		name="sub_organization" 
	                    		v-model="sb_select" 
	                    		v-on:change="loadRoute()">
	                        <option v-for="data in SbData" v-bind:value="data.id">
	                        	@{{data.organization_name}}
	                        </option>
	                    </select>
	                </div>

	                <div class="form-group">
	                	<label for="segment" class="control-label">Segment</label> 
	                	<select id="segment" 
	                			name="segment" 
	                			class="form-control"
	                			v-model="segment_select"
	                			v-on:change="drawCanvas(segment_select)">
	                		<option v-for="data in segmentData" v-bind:value="data.id">
	                			@{{data.segment_info}}
	                		</option>
	                	</select>
	                </div>
	            </div>
	        </div>
	        {!! Form::close() !!}   
	    </div>
	    @box_close()
	</article>
</template>

<script>
  	Vue.component('manager', {
	    template: '#scope-manager',
	    data: function() {
	    	return {
	    		dataPass: {!! json_encode($data) !!},
				rmb_select: {!! json_encode($data) !!}['1']['value'],
				sb_select: '',
				route_select: '',
				segment_select: '',
				branch_no_select: '',
				SbData: [],
				routeData: [],
				segmentData: []	
			}
	    },
	    created: function() {
	    	this.loadSB();
		},
		methods: {
			loadSB: function() {
				var vm = this;
				var url = '/ajax/rmb/'+ this.rmb_select + '/sb';

				axios.get(url)
				.then(function(rs) {
					vm.sb_select = rs.data[0]['id'];
					vm.SbData = rs.data;
					vm.loadRoute();
				})
				.catch(function(err) {
					console.log(err);
				});
			},

			loadRoute: function() {
				var vm = this;
	          	var url = '/ajax/sb/'+ vm.sb_select +'/route?rmb_id='+ vm.rmb_select;

	          	axios.get(url)
				.then(function(rs) {
					vm.route_select = rs.data[0]['id'];
					vm.routeData = rs.data;
					vm.loadSegment();
				})
				.catch(function(err) {
					console.log(err);
				});
			},

			loadSegment: function() {
				var vm = this;
				var url = '/ajax/route/'+ vm.route_select +'/segment?sb_id='+ vm.sb_select;

				axios.get(url)
				.then(function(rs) {
					vm.segment_select = rs.data[0]['id'];
					vm.segmentData = rs.data;
					vm.drawCanvas();
				})
				.catch(function(err) {
					console(err)
				});
			},

			drawCanvas: function() {
				this.$root.$emit('drawCanvas', {segmentId: this.segment_select});
			}
		}
	});

</script>

@include('front-end.m13.inputting_system.refactor_script.streching')