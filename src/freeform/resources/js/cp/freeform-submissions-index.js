"undefined"==typeof Craft.Freeform&&(Craft.Freeform={}),Craft.Freeform.SubmissionsIndex=Craft.BaseElementIndex.extend({getViewClass:function(e){switch(e){case"table":return Craft.Freeform.SubmissionsTableView;default:return this.base(e)}},getDefaultSort:function(){return["dateCreated","desc"]}}),Craft.registerElementIndexClass("Freeform_Submission",Craft.Freeform.SubmissionsIndex);