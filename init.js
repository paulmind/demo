Ext.require(['Ext.data.*', 'Ext.grid.*']);

Ext.define('User', {
		extend: 'Ext.data.Model',
		fields: [{
				name: 'id',
				type: 'int',
				useNull: true
		}, 'userName', 'eduName', 'cityName'],
		validations: [{
				type: 'length',
				field: 'userName',
				min: 3
		}, {
				type: 'length',
				field: 'eduName',
				min: 1
		}, {
				type: 'length',
				field: 'cityName',
				min: 1
		}]
});

Ext.define('City', {
		extend: 'Ext.data.Model',
		fields: [{
				name: 'id',
				type: 'int',
				useNull: true
		}, 'cityName'],
		validations: [{
				type: 'length',
				field: 'cityName',
				min: 2
		}]
});

Ext.define('Edu', {
		extend: 'Ext.data.Model',
		fields: [{
				name: 'id',
				type: 'int',
				useNull: true
		}, 'eduName'],
		validations: [{
				type: 'length',
				field: 'eduName',
				min: 2
		}]
});

Ext.onReady(function(){

		var userStore = Ext.create('Ext.data.Store', {
				autoLoad: true,
				autoSync: true,
				model: 'User',
				proxy: {
						type: 'rest',
						url: 'app.php/users',
						reader: {type: 'json', root: 'data'},
						writer: {type: 'json'},
						pageParam: undefined,
						startParam: undefined,
						limitParam: undefined
				},
				listeners:{
					update: function(){
						var rawRes = userStore.proxy.reader.rawData;
						console.log(rawRes);
					}
				}
		});

		var cityStore = Ext.create('Ext.data.Store', {
				autoLoad: true,
				autoSync: true,
				model: 'City',
				proxy: {
						type: 'rest',
						url: 'app.php/cities',
						reader: {type: 'json', root: 'data'},
						writer: {type: 'json'},
						pageParam: undefined,
						startParam: undefined,
						limitParam: undefined
				}
		});

		var eduStore = Ext.create('Ext.data.Store', {
				autoLoad: true,
				autoSync: true,
				model: 'Edu',
				proxy: {
						type: 'rest',
						url: 'app.php/educations',
						reader: {type: 'json', root: 'data'},
						writer: {type: 'json'},
						pageParam: undefined,
						startParam: undefined,
						limitParam: undefined
				}
		});

		var userEditing = Ext.create('Ext.grid.plugin.RowEditing');
		var cityEditing = Ext.create('Ext.grid.plugin.RowEditing');
		var eduEditing = Ext.create('Ext.grid.plugin.RowEditing');

		var userGrid = Ext.create('Ext.grid.Panel', {
				// renderTo: 'example-grid',
				plugins: [userEditing],
				id: 'foo',
				height: 300,
				border: false,
				store: userStore,
				columns: [
					{text: 'ID', width: 40, sortable: true, dataIndex: 'id'},
					{text: 'Пользователь', width: 160, sortable: true, dataIndex: 'userName', field: {xtype: 'textfield'}},
					{text: 'Образование', width: 100, sortable: true, dataIndex: 'eduName', 
						editor: {
							xtype: 'combobox',
							editable: false,
							// itemId: 'combo',
							queryMode: 'remote',
							displayField: 'eduName',

							allowBlank: false,

							triggerAction: 'all',
							store: eduStore,

						}
				},
					{
						text: 'Города',
						flex: 1,
						sortable: true,
						dataIndex: 'cityName',
						editor: {
							xtype: 'combobox',
							editable: false,
							itemId: 'combo',
							queryMode: 'remote',
							displayField: 'cityName',

							allowBlank: false,

							triggerAction: 'all',
							multiSelect: true,
							store: cityStore,
							listeners: {
								// scope: this,
								focus: function(combo){
									// HACK
									var rawRes = userStore.proxy.reader.rawData;
									// console.log(rawRes.success);
									// console.log(rawRes.message);
									var rawData = userGrid.getView().getSelectionModel().getSelection()[0].raw;
									var rawValue = rawData.cityName;
									var rawValueId = rawData.id;
									console.log(rawData.id+'-'+rawData.userName+'-'+rawData.eduName+'-'+rawValue);

									if(rawRes.success && rawRes.message == 'record updated' && rawValueId){
										console.log(rawRes.data[0].cityName);

										if(rawValueId == rawRes.data[0].id){
											console.log('HACK');
											rawValue = rawRes.data[0].cityName;
										}
										
									}

									if(rawValue){
										combo.setValue(rawValue.split(', '));
									}
								}
							}
						}
				}],
				dockedItems: [{
						xtype: 'toolbar',
						items: [{
								text: 'Добавить',
								iconCls: 'icon-add',
								handler: function(){
										userStore.insert(0, new User());
										userEditing.startEdit(0, 0);
								}
						}, '-', {
								text: 'Удалить',
								iconCls: 'icon-delete',
								handler: function(){
										var selection = userGrid.getView().getSelectionModel().getSelection()[0];
										if (selection) {
												userStore.remove(selection);
										}
								}
						}]
				}]
		});


		var cityGrid = Ext.create('Ext.grid.Panel', {
				// renderTo: 'example-grid',
				plugins: [cityEditing],
				height: 300,
				border: false,
				store: cityStore,
				columns: [
					{text: 'ID', width: 40, sortable: true, dataIndex: 'id'},
					{text: 'Город', flex: 1, sortable: true, dataIndex: 'cityName', field: {xtype: 'textfield'}},
				],
				dockedItems: [{
						xtype: 'toolbar',
						items: [{
								text: 'Добавить',
								iconCls: 'icon-add',
								handler: function(){
										cityStore.insert(0, new City());
										cityEditing.startEdit(0, 0);
								}
						}, '-', {
								text: 'Удалить',
								iconCls: 'icon-delete',
								handler: function(){
										var selection = cityGrid.getView().getSelectionModel().getSelection()[0];
										if (selection) {
												cityStore.remove(selection);
										}
								}
						}]
				}]
		});


		var eduGrid = Ext.create('Ext.grid.Panel', {
				// renderTo: 'example-grid',
				plugins: [eduEditing],
				height: 300,
				border: false,
				store: eduStore,
				columns: [
					{text: 'ID', width: 40, sortable: true, dataIndex: 'id'},
					{text: 'Образование', flex: 1, sortable: true, dataIndex: 'eduName', field: {xtype: 'textfield'}},
				],
				dockedItems: [{
						xtype: 'toolbar',
						items: [{
								text: 'Добавить',
								iconCls: 'icon-add',
								handler: function(){
										eduStore.insert(0, new Edu());
										eduEditing.startEdit(0, 0);
								}
						}, '-', {
								text: 'Удалить',
								iconCls: 'icon-delete',
								handler: function(){
										var selection = eduGrid.getView().getSelectionModel().getSelection()[0];
										if (selection) {
												eduStore.remove(selection);
										}
								}
						}]
				}]
		});




	var tabs = Ext.createWidget('tabpanel', {
				renderTo: 'example-grid',
				width: 600,
				activeTab: 0,
				defaults :{
						// bodyPadding: 10
				},
				items: [
				{
						title: 'Пользователи',
						// closable: true
						items: userGrid
				},{
						title: 'Города',

						items: cityGrid
				},{
						title: 'Образование',

						items: eduGrid
				}
				]
		});

});