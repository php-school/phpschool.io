import{m as Z,a as G,o as i,c,r as Y,b as Q,d as ee,e as te,f as A,g as V,h as se,i as a,j as e,w as g,v as w,k as v,l as u,t as _,n as oe,p as $,q as h,s as T,F as S,u as F,x as b,y as ne,z as j,A as R,B as le,C as O,D as ie,E as B,G as re,H as ce,I as de,J as C,K as ae,L as ue,M as fe,N as he}from"./vendor.ae6acc5d.js";(function(){const t=document.createElement("link").relList;if(t&&t.supports&&t.supports("modulepreload"))return;for(const o of document.querySelectorAll('link[rel="modulepreload"]'))r(o);new MutationObserver(o=>{for(const n of o)if(n.type==="childList")for(const f of n.addedNodes)f.tagName==="LINK"&&f.rel==="modulepreload"&&r(f)}).observe(document,{childList:!0,subtree:!0});function l(o){const n={};return o.integrity&&(n.integrity=o.integrity),o.referrerpolicy&&(n.referrerPolicy=o.referrerpolicy),o.crossorigin==="use-credentials"?n.credentials="include":o.crossorigin==="anonymous"?n.credentials="omit":n.credentials="same-origin",n}function r(o){if(o.ep)return;o.ep=!0;const n=l(o);fetch(o.href,n)}})();const y=(s,t)=>{const l=s.__vccOpts||s;for(const[r,o]of t)l[r]=o;return l},me={props:{file_path:String,file_content:String,readonly:{default:!1,type:Boolean}},mounted(){this._editor=Z(G.edit(this.$el,{minLines:20,maxLines:20})),this._editor.setTheme("ace/theme/monokai"),this._editor.session.setMode("ace/mode/php"),this._editor.setValue(this.file_content),this.readonly&&this._editor.setReadOnly(!0)},beforeUnmount(){this._editor.destroy()}};function pe(s,t,l,r,o,n){return i(),c("div")}const W=y(me,[["render",pe]]),_e={components:{FolderIcon:Y,DocumentIcon:Q,FolderOpenIcon:ee,PencilIcon:te,FolderPlusIcon:A,PlusIcon:V,TrashIcon:se},inject:["state"],name:"tree-item",props:{parent:Array,model:Object,deleteFunction:Function,fileSelectFunction:Function,showControls:{type:Boolean,default:!1},customStyles:{type:Object}},data(){return{isOpen:!1,isEditing:!1}},computed:{isFolder(){return this.model.children},hasChildren(){return this.model.children.length},sortedChildren(){const s=this.model.children.filter(r=>r.children),t=this.model.children.filter(r=>!r.children),l=(r,o)=>r.name.toLowerCase()<o.name.toLowerCase()?-1:r.name.toLowerCase()>o.name.toLowerCase()?1:0;return s.sort(l).concat(t.sort(l))},isBeingEdited(){return this.isEditing||"new"in this.model&&this.model.new}},methods:{selectNode(s){if(!this.isEditing){if(this.isFolder){this.isOpen=!this.isOpen;return}this.state.selectedFile=s,this.fileSelectFunction&&this.fileSelectFunction(s)}},addFile(){this.model.children.filter(s=>"new"in s).length||(this.model.children.push({name:"new file",new:!0}),this.isOpen=!0)},addFolder(){this.model.children.filter(s=>"new"in s).length||(this.model.children.push({name:"new folder",children:[],new:!0}),this.isOpen=!0)},saveName(){if(this.parent.filter(t=>t!==this.model).map(t=>t.name).includes(this.model.name)){alert("Name must be unique");return}this.model.new&&delete this.model.new,this.isEditing=!1},deleteChild(s){if(this.deleteFunction(s)){const l=this.parent.findIndex(r=>r===s);this.parent.splice(l,1)}}}},xe={class:"flex items-center"},ge={key:3,class:"ml-2"},we={key:0,class:"hidden group-hover:flex"},ye={key:0,class:"ml-3"};function ve(s,t,l,r,o,n){var L,N;const f=a("FolderIcon"),d=a("FolderOpenIcon"),m=a("DocumentIcon"),p=a("PencilIcon"),I=a("FolderPlusIcon"),x=a("PlusIcon"),U=a("TrashIcon"),J=a("tree-item",!0);return i(),c("li",{class:b([[l.model===n.state.selectedFile&&(N=(L=l.customStyles)==null?void 0:L.selectedFileClasses)!=null?N:""],"flex flex-col pl-3 py-2 w-full"])},[e("div",{onClick:t[4]||(t[4]=k=>n.selectNode(l.model)),class:"group flex w-full items-center justify-between cursor-pointer"},[e("div",xe,[n.isFolder?g((i(),v(f,{key:0,class:"mr-1 h-5 w-5",style:{fill:"none !important"}},null,512)),[[w,!o.isOpen||!n.hasChildren]]):u("",!0),n.isFolder&&n.hasChildren?g((i(),v(d,{key:1,class:"mr-1 h-5 w-5",style:{fill:"none !important"}},null,512)),[[w,o.isOpen]]):u("",!0),n.isFolder?u("",!0):(i(),v(m,{key:2,class:"mr-1 h-5 w-5",style:{fill:"none !important"}})),g(e("span",{class:"hover:text-white"},_(l.model.name),513),[[w,!n.isBeingEdited]]),g(e("input",{onKeyup:t[0]||(t[0]=$((...k)=>n.saveName&&n.saveName(...k),["enter"])),class:"bg-gray-700 p-1 rounded-sm","onUpdate:modelValue":t[1]||(t[1]=k=>l.model.name=k)},null,544),[[w,n.isBeingEdited],[oe,l.model.name]]),n.isFolder&&n.hasChildren?(i(),c("span",ge,"["+_(o.isOpen?"-":"+")+"]",1)):u("",!0)]),l.showControls?g((i(),c("div",we,[h(p,{onClick:t[2]||(t[2]=T(k=>o.isEditing=!0,["stop"])),class:"mr-2 h-5 w-5 cursor-pointer hover:text-pink-500",style:{fill:"none !important"}}),n.isFolder?(i(),v(I,{key:0,onClick:T(n.addFolder,["stop"]),class:"mr-2 h-5 w-5 cursor-pointer hover:text-pink-500",style:{fill:"none !important"}},null,8,["onClick"])):u("",!0),n.isFolder?(i(),v(x,{key:1,onClick:T(n.addFile,["stop"]),class:"mr-2 h-5 w-5 cursor-pointer hover:text-pink-500",style:{fill:"none !important"}},null,8,["onClick"])):u("",!0),h(U,{onClick:t[3]||(t[3]=T(k=>n.deleteChild(l.model),["stop"])),class:"mr-2 h-5 w-5 cursor-pointer hover:text-pink-500 fill-none",style:{fill:"none !important"}})],512)),[[w,!n.isBeingEdited]]):u("",!0)]),n.isFolder?g((i(),c("div",ye,[e("ul",{class:b([{"mt-1":n.hasChildren},"w-full text-gray-300"])},[(i(!0),c(S,null,F(n.sortedChildren,k=>(i(),v(J,{parent:l.model.children,model:k,"delete-function":l.deleteFunction,"file-select-function":l.fileSelectFunction,"custom-styles":l.customStyles,"show-controls":l.showControls},null,8,["parent","model","delete-function","file-select-function","custom-styles","show-controls"]))),256))],2)],512)),[[w,o.isOpen]]):u("",!0)],2)}const z=y(_e,[["render",ve]]),ke={components:{TreeItem:z,FolderPlusIcon:A,PlusIcon:V},props:{deleteFunction:Function,fileSelectFunction:Function,initialSelectedItem:Object,showControls:{type:Boolean,default:!1},files:Array,customStyles:{type:Object}},provide(){return{state:ne(()=>this.state)}},data(){return{state:{selectedFile:this.initialSelectedItem}}},methods:{addFile(){this.files.filter(s=>"new"in s).length||this.files.push({name:"new file",new:!0})},addFolder(){this.files.filter(s=>"new"in s).length||this.files.push({name:"new folder",children:[],new:!0})}}},be={class:"border border-solid border-gray-600 rounded"},Ce={class:"border-b border-solid border-gray-600 p-3 flex justify-between"},Se=e("span",{class:"text-white text-base font-mono"},"Files",-1),Fe={key:0,class:"flex text-white"},Ie={class:"w-full text-gray-300 font-mono p-1"};function Oe(s,t,l,r,o,n){const f=a("FolderPlusIcon"),d=a("PlusIcon"),m=a("tree-item");return i(),c("div",be,[e("div",Ce,[Se,l.showControls?(i(),c("div",Fe,[h(f,{onClick:n.addFolder,class:"mr-2 h-5 w-5 cursor-pointer hover:text-pink-500",style:{fill:"none !important"}},null,8,["onClick"]),h(d,{onClick:n.addFile,class:"mr-2 h-5 w-5 cursor-pointer hover:text-pink-500",style:{fill:"none !important"}},null,8,["onClick"])])):u("",!0)]),e("ul",Ie,[(i(!0),c(S,null,F(l.files,p=>(i(),v(m,{parent:l.files,model:p,"delete-function":l.deleteFunction,"file-select-function":l.fileSelectFunction,"custom-styles":l.customStyles,"show-controls":l.showControls},null,8,["parent","model","delete-function","file-select-function","custom-styles","show-controls"]))),256))])])}const P=y(ke,[["render",Oe]]),Te={components:{XMarkIcon:j},setup(s,{slots:t}){const l=R(t.default().map(o=>o.props.title)),r=R(l.value[0]);return le("selectedTitle",r),{tabTitles:l,selectedTitle:r}},methods:{closeTab(s){console.log("close tab")}}},$e={class:"tabs"},je={class:"tabs list-reset flex justify-start mb-1"},Pe=["onClick"],Me={href:"#",class:""};function Ee(s,t,l,r,o,n){const f=a("XMarkIcon");return i(),c("div",$e,[e("ul",je,[(i(!0),c(S,null,F(r.tabTitles,d=>(i(),c("li",{key:d,class:b([{"border-pink-500":d===r.selectedTitle},"flex border-solid border-t-2 mr-1 bg-stone-700 inline-block py-2 px-4 text-white hover:text-pink-500 text-xs no-underline items-center"]),onClick:m=>r.selectedTitle=d},[e("a",Me,_(d),1),h(f,{onClick:t[0]||(t[0]=m=>n.closeTab(s.tab)),class:"cursor-pointer ml-2 w-3 h-3 text-zinc-400"})],10,Pe))),128))]),O(s.$slots,"default")])}const X=y(Te,[["render",Ee]]),Le={props:{title:String,showCloseButton:{type:String,default:"default"}},setup(){return{selectedTitle:ie("selectedTitle")}}},Ne={class:"tabs-panel flex"};function Re(s,t,l,r,o,n){return g((i(),c("div",Ne,[O(s.$slots,"default")],512)),[[w,l.title===r.selectedTitle]])}const H=y(Le,[["render",Re]]),Ae={components:{XMarkIcon:j},props:{size:{type:String,default:"2xl"}},data(){return{modalSizeClasses:{xs:"max-w-xs",sm:"max-w-sm",md:"max-w-md",lg:"max-w-lg",xl:"max-w-xl","2xl":"max-w-2xl","3xl":"max-w-3xl","4xl":"max-w-4xl","5xl":"max-w-5xl","6xl":"max-w-6xl","7xl":"max-w-7xl"}}},methods:{closeModal(s){this.$emit("close")}}},Ve=e("div",{class:"bg-gray-900 bg-opacity-80 fixed inset-0 z-40"},null,-1),Be={tabindex:"-1",class:"overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center flex"},We={class:"relative rounded-lg shadow bg-gray-800"},ze={key:0,class:"p-6 rounded-b border-t border-gray-600"};function Xe(s,t,l,r,o,n){const f=a("XMarkIcon");return i(),c("div",null,[Ve,e("div",Be,[e("div",{class:b(["relative p-4 w-full h-full md:h-auto",o.modalSizeClasses[l.size]])},[e("div",We,[e("div",{class:b(["p-4 rounded-t flex justify-between items-center",s.$slots.header?"border-b border-solid border-gray-600":""])},[O(s.$slots,"header"),e("div",null,[e("button",{onClick:t[0]||(t[0]=(...d)=>n.closeModal&&n.closeModal(...d)),type:"button",class:"text-gray-400 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center hover:bg-gray-600 hover:text-white"},[h(f,{class:"w-5 h-5"})])])],2),e("div",{class:b(["p-6",s.$slots.header?"":"pt-0"])},[O(s.$slots,"body")],2),s.$slots.footer?(i(),c("div",ze,[O(s.$slots,"footer")])):u("",!0)])],2)])])}const M=y(Ae,[["render",Xe]]),He={components:{TrophyIcon:B,UserCircleIcon:re},props:{student:Object},methods:{login(){window.location.href="/student-login"}}},qe={class:"w-1/3 mx-auto mt-10"},De={class:"flex justify-between"},Ke=e("svg",{class:"h-10 w-10 mr-4",role:"img",focusable:"false",viewBox:"0 0 24 24","aria-label":"GitHub",fill:"currentColor",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M12,2 C14.777778,2 17.1388887,2.99290757 19.0833333,4.9787234 C21.027778,6.96453923 22,9.3758863 22,12.212766 C22,13.6312054 21.722222,15.0141842 21.1666667,16.3617021 C20.6111113,17.7092201 19.8125,18.8439714 18.7708333,19.7659574 C17.7291667,20.6879435 16.5138887,21.4326243 15.125,22 C14.7083333,22 14.5,21.787234 14.5,21.3617021 L14.5,20.4042553 L14.5,18.5957447 C14.5,17.7446809 14.2916667,17.106383 13.875,16.6808511 C16.9305553,16.3971629 18.4583333,14.6950352 18.4583333,11.5744681 C18.4583333,10.5815605 18.1111113,9.73049668 17.4166667,9.0212766 C17.6944447,8.17021277 17.625,7.24822672 17.2083333,6.25531915 C16.652778,6.1134754 15.75,6.46808511 14.5,7.31914894 C13.6666667,7.03546077 12.8333333,6.89361702 12,6.89361702 C11.1666667,6.89361702 10.3333333,7.03546077 9.5,7.31914894 C8.94444467,6.89361702 8.42361133,6.60992885 7.9375,6.46808511 C7.45138867,6.32624136 7.13888867,6.25531915 7,6.25531915 L6.79166667,6.25531915 C6.375,7.24822672 6.30555533,8.17021277 6.58333333,9.0212766 C6.027778,9.73049668 5.75,10.5815605 5.75,11.5744681 C5.75,14.6950352 7.20833333,16.3971629 10.125,16.6808511 C9.847222,16.9645392 9.63888867,17.4609927 9.5,18.1702128 C8.25,18.7375884 7.277778,18.4539009 6.58333333,17.3191489 C6.16666667,16.6099289 5.68055533,16.2553191 5.125,16.2553191 C4.70833333,16.2553191 4.534722,16.3262414 4.60416667,16.4680851 C4.67361133,16.6099289 4.847222,16.7517733 5.125,16.893617 C5.54166667,17.1773052 5.88888867,17.6737586 6.16666667,18.3829787 C6.58333333,19.5177307 7.69444467,19.8723404 9.5,19.4468085 L9.5,20.7234043 L9.5,21.3617021 C9.5,21.787234 9.29166667,22 8.875,22 C6.79166667,21.2907799 5.125,20.0496456 3.875,18.2765957 C2.625,16.5035459 2,14.4822693 2,12.212766 C2,9.3758863 2.972222,6.96453923 4.91666667,4.9787234 C6.86111133,2.99290757 9.222222,2 12,2 L12,2 Z"})],-1),Ue=e("span",null,"Log in with GitHub",-1),Je=[Ke,Ue],Ze={key:1,class:"flex items-center space-x-4"},Ge=["src"],Ye={key:1,class:"w-10 h-10 rounded-full"},Qe={class:"font-medium"},et={class:"text-sm text-gray-500 dark:text-gray-400"},tt={class:"flex items-center"},st={class:"flex rounded-lg bg-yellow-400 p-2"},ot=e("p",{class:"ml-2 text-sm font-medium text-gray-500 dark:text-gray-400"},"8 out of 23",-1),nt=e("div",{class:"w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-4"},[e("div",{class:"bg-pink-600 h-2.5 rounded-full dark:bg-pink-500",style:{width:"45%"}})],-1);function lt(s,t,l,r,o,n){const f=a("UserCircleIcon"),d=a("TrophyIcon");return i(),c("div",qe,[e("div",De,[l.student?u("",!0):(i(),c("button",{key:0,onClick:t[0]||(t[0]=(...m)=>n.login&&n.login(...m)),class:"w-1/2 flex items-center p-2 bg-[#24292F] hover:bg-[#24292F]/90 focus:ring-4 font-medium focus:outline-none focus:ring-[#24292F]/50 text-center text-white rounded-lg"},Je)),l.student?(i(),c("div",Ze,[l.student.profile_picture?(i(),c("img",{key:0,class:"w-10 h-10 rounded-full",src:l.student.profile_picture,alt:""},null,8,Ge)):u("",!0),l.student.profile_picture?u("",!0):(i(),c("div",Ye,[h(f,{class:"text-pink-600"})])),e("div",Qe,[e("div",null,_(l.student.name),1),e("div",et,"Joined in "+_(l.student.join_date),1)])])):u("",!0),e("div",tt,[e("span",st,[h(d,{class:"h-6 w-6 text-white"})]),ot])]),nt])}const it=y(He,[["render",lt]]),q="/dist/assets/core-workshops.3ee706aa.png",rt={components:{ArrowRightIcon:ce},props:{workshops:Object},data(){return{selectedWorkshop:null}},methods:{selectWorkshop(s){this.selectedWorkshop=this.workshops.find(t=>t.code===s)},selectExercise(s){window.location.href="/cloud/workshop/"+this.selectedWorkshop.code+"/exercise/"+s.slug}}},ct={class:"w-full grid grid-cols-4 gap-4 mt-10 mb-10"},dt={class:"col-span-2"},at={id:"workshops",class:"flex flex-col items-center justify-center bg-gray-800 rounded-lg shadow"},ut=e("div",{class:"px-4 py-5 sm:px-6 border-b w-full"},[e("h3",{class:"text-lg leading-6 font-medium text-white"}," PHP School Workshops "),e("p",{class:"mt-1 max-w-2xl text-sm text-gray-200"}," Pick a workshop to try it out online! ")],-1),ft={id:"workshops-list",class:"flex flex-col divide divide-y"},ht=["onClick"],mt={class:"select-none cursor-pointer flex flex-1 items-center p-4"},pt=e("div",{class:"flex flex-col w-10 h-10 justify-center items-center mr-4"},[e("a",{href:"#",class:"block relative"},[e("img",{alt:"workshop",src:q,class:"mx-auto object-cover h-10 w-10"})])],-1),_t={class:"flex-1 pl-1 mr-16 text-white group-hover:text-pink-600"},xt={class:"font-medium"},gt={class:"text-gray-300 text-sm"},wt={class:"text-gray-200 text-xs bg-pink-600 py-1 px-3 rounded-full"},yt={href:"#",class:"w-24 text-right flex justify-end"},vt={class:"col-span-2"},kt={key:0,class:"flex flex-col items-center justify-center h-full"},bt=e("h1",{class:"text-2xl upppercase"},"Select a workshop",-1),Ct=e("svg",{class:"-scale-x-100",xmlns:"http://www.w3.org/2000/svg","xmlns:xlink":"http://www.w3.org/1999/xlink",id:"Layer_0_Image","data-name":"Layer 0\xA0Image",viewBox:"0 0 800 600",x:"0px",y:"0px",width:"300px",height:"200px",version:"1.1"},[e("title",null,"1"),e("path",{d:"M129.233,225.46c-.246-.981-.411-1.637-.667-2.657l-1.549,1.154c-3.607-2.008-7.226-4.148-10.958-6.069-5-2.575-10.124-4.779-14.924-7.916-4.072-2.663-9.226-4.192-12.641-7.664-3.228-3.28-8.415-2.9-10.887-6.935-4.928-.659-7.356-5.3-11.5-7.268-1.217-.577-2.039-1.632-3.684-1.727s-1.636-.493-3.493-3.988c-.509-.071-1.174-.185-1.844-.248-.478-.044-1.3.143-1.394-.047-2.19-4.642-8.31-3.48-10.922-7.439-.247-.375-.406-1.093-.675-1.129-7.643-1.011-12.234-7.67-19-10.223-2.536-.956-3.559-3.6-5.942-4.633-1.022-.443-1.961-1.078-3.061-1.694a16.567,16.567,0,0,0-1.024-3.726,23.393,23.393,0,0,0-3.026-3.6c3.894-1.724,7.54-.361,10.844-2.127l-8.21-.384-.509-1.888c9.213-2.952,9.34-3.263,20.148,1.163,7.647,3.131,15.885,5.18,22.436,10.67,6.281,1.417,11.267,5.512,16.878,8.3,7.65,3.8,14.907,8.381,22.477,12.353,9.526,5,19.165,9.783,28.815,14.54,9.034,4.454,18.346,8.225,27.741,11.894,6.2,2.421,12.33,5.265,18.542,7.789s12.513,4.842,18.792,7.2c4.606,1.733,9.125,4.022,13.9,4.907,5.051.936,8.831,4.5,13.778,5.406,3.119.57,5.917,2.641,9.314,2.039.47-.083,1.107-.211,1.429.018,5.339,3.793,11.89,4.021,17.823,6.046,6.166,2.1,12.477,3.8,18.768,5.509,3.477.946,7.051,1.432,10.424,2.885,3.98,1.713,8.45,1.469,12.6,2.86a131.7,131.7,0,0,0,15.74,3.951q7.7,1.56,15.321,3.461c9.885,2.47,20.081,3.592,29.754,6.994,2.56.9,5.318.333,8.119,1.42,3.774,1.465,8.785.15,12.212,1.99,3.783,2.031,8.173-.636,11.293,2.609,7.5-.214,14.541,2.906,22.024,2.976,2.223.02,4.434.916,6.658,1.375,2.457.507,4.912,1.028,7.385,1.441,2.246.377,4.848-.909,6.654,1.469,3.8-.507,7.368,1.078,11.021,1.3,4.685.29,9.105,2.674,13.906,1.6,2.651,2.955,6.988-.037,9.78,2.769.489.49,1.85.105,2.808.136,2.773.093,5.441-.1,8.182,1.309,2.345,1.2,5.359-.571,8.178,1.39,1.621,1.128,4.669.2,6.973.2l2.695,2.695c3.962-2.694,7.239-.9,10.435,1.594,2.044-2.452,5.321-2.066,7.182-1.041,2.4,1.32,4.57,1.022,6.832,1.062,6.609.119,13.017,2.28,19.736,1.653,1.726-.161,3.63,1.585,5.4,2.43a19.312,19.312,0,0,1,2.949-1.105c2.969-.624,5.8.907,9,.21,2.653-.577,5.954.491,8.585,1.677,1.449.653,1.943-.338,2.632-.276,3.911.354,8.021-1.595,11.8,1.163.949.691,2.828.1,4.28.1,10.4,0,20.808-.032,31.211.025,2.346.013,4.849-.115,6.99.643,2.2.778,3.52-1.763,5.629-.731,1.363.668,3.021.732,4.8,1.123a31.688,31.688,0,0,1,5.58-1.008c7.74-.128,15.513-.482,23.218.057,9.591.671,18.943-1.389,28.427-1.83,6.838-.317,13.56-3.124,20.868-4.955l5.574,2.043,1.4-1.239,4.179,5.03-11.6,2.957L704.32,301H711.8l2.835,2.834-5.252,3.915,3.186,1.236c.881,4.02-1.752,6.433-4.155,8.92.457.51.908,1.015,1.021,1.142l-5.45,5.38c-1.923-.46-4.309-2.971-7.168-.765.455.514.911,1.026,1.775,2l-8.683-1.406-.27,1.107,4.444,1.068-5.18,2.692c-.409-.846-.763-1.575-1.444-2.98l-5.116-.3c.611,1.442,1.056,2.493,1.871,4.415-1.771,0-2.906.08-4.027-.013-8.1-.674-15.938,2.08-24.085,1.576-8.195-.506-16.457.114-24.663-.291-1.749-.087-2.814,2.63-4.866.64-.635-.614-3.157.73-3.8.115-2.339-2.225-3.157,1.156-5.037,1.018-3.264-.239-6.1-1.868-9.556-1.736-10.388.4-20.8.272-31.2.076-3.92-.074-7.815-1.094-11.742-1.331-4.027-.243-8.079-.054-12.106-.344,5.925-1.653,19.9-1.465,26.215-.588q-1.623-.489-3.246-.975l.96-1.051c-4.343,0-8.7-.24-13.024.057-6.195.425-12.06-2.58-18.274-1.509-2.177-1.93-5.326-.544-7.271-1.65-3.1-1.764-5.7,1.53-8.881-.352-2.4-1.421-5.958-.884-9.651-1.289l-1.98-1.612q.225-.48.451-.959l21.363,2.253,1.644-1.43-2.144-2.142c-.706.525-1.411,1.051-2.935,2.188l-1.181-4.774-1.5,1.271-2.157,1.07-.485-2.349-9.1-2.219c-.624,1.324-1.093,2.318-1.626,3.447l-7.4-2.695-2.552,1.52-4.123-5.223-1.638,1.638-3.915-1.913,2.179-2.111,1.739,1.518,4.2-1.923,1,2.884,6.285-2.132c2.826,2.259,6.5.385,10.169,1.2l2.83,2.678c2.017-2.637,4.966-.763,7.743-1.708.04-.647.091-1.473.181-2.942-1.033.8-1.508,1.174-2.373,1.849l-2.3-3.425-2.993,3.182-4.644-2.386c-2.117,2.085-4.194.9-6.562-.257-2-.973-4.347-1.215-6.689-1.819L509.18,301.3c-.868,2.1-1.26,3.052-1.848,4.478-2.117-.309-4.377-.637-6.636-.971-2.278-.338-4.979-.037-6.73-1.183-2.351-1.539-3.814,2.026-6.209.139-1.606-1.265-4.376-1.052-6.6-1.5-1.017,1.49-1.779,2.606-3.207,4.7l6.814-.616,1.347,1.243-1.209,1.558,1.141,1.39c-.251.26-.55.814-.808.8-2.117-.151-4.223-.543-6.339-.6-2.274-.056-5.1,1.064-6.706.078-2.441-1.5-4.693-.331-7.131-1.055l-3.228-3.072-3.836,2.756c7.337,3.523,14.933,3.845,23.326,5.563a21.815,21.815,0,0,1-9.763.567c-1.683-.219-3.217-1.518-5.231-1.365-1.6.122-3.869-1.145-4.749-.437-2.245,1.8-2.858-.993-4.329-.958a39.948,39.948,0,0,1-5.8-.037c-2.244-.277-4.426-1.039-6.668-1.368a39.7,39.7,0,0,1-6.007-1.222c-3.249-.992-7.023-.266-10.6.862l8.308.7c-6.726,2.937-12.06-1.026-17.556-.378-1.07-1.461-1.935-3.14-3.271-4.254-.673-.561-2.2-.1-4.869-.1l4.208,2.062c-.337.3-.669.828-.952.8-2.975-.268-5.928.572-8.934-1.255-2.216-1.346-5.247.151-8.184-1.228-3.394-1.594-7.715-1.1-11.554-1.893-3.774-.782-7.586-1.334-11.337-2.355-5.486-1.494-11.235-2.015-16.733-3.474s-11.394-.884-16.582-3.763c-2.615-1.451-5.719-.608-8.7-1.727-6.309-2.366-13.134-3.335-19.713-5.013-3.513-.9-6.829-2.408-10.577-2.544a10.028,10.028,0,0,1-9.3-5.8c-.512,1.052-.95,1.951-1.424,2.923l-1.547-1.375-.984.882c-2.679-.868-5.029-2.285-7.337-2.219-3.154.09-5.03-2.05-9.113-2.864-6.409,1.326-12.82-4.678-20.756-4.714-.194-1.29-.394-2.623-.784-5.224-1.141,1.992-1.824,3.183-2.01,3.507a44.671,44.671,0,0,1-16.4-6.2v-2.009a12.2,12.2,0,0,1-2.773,1.319,23.145,23.145,0,0,1-3.831.051l-2.376-2.737H216.7c-.373-1.051-.671-1.892-1.046-2.947l-2.512,1.6-4.154-3.745c-.172-1.6-.291-2.713-.506-4.712l-3.594,5.386c-4.34-1.713-8.286-4.254-13.139-5.123-4.281-.767-7.8-4.13-12.262-5.4-2.462-.7-4.451-3.3-7.58-2.652-1.5-2.422-4.164-2.641-6.5-3.587-2.92-1.184-6.759-.449-8.8-3.109-2.059-2.682-6.253-1.153-7.628-4.538-3-.338-5.412-2.437-8.118-3.081a17.5,17.5,0,0,1-7.835-4.029C132.275,225.581,130.816,225.749,129.233,225.46Zm310.409,60.372c-2.01.32-3.834,1.22-4.981.681-3.829-1.8-7.97-1.861-11.951-2.648-4.879-.965-10.126-.7-14.79-1.845-6.408-1.565-12.961-2.722-19.363-3.7-6.6-1.01-13.026-3.072-19.778-3.387-3.023-.141-5.968-1.823-8.972-2.727-.323-.1-.8.329-1.324.562.041.615.085,1.27.141,2.106,3.966.56,8.166.357,11.608,1.873,3.09,1.361,6.49.048,8.79,1.455,2.765,1.692,5.925,0,7.994,1.713,2.434,2.009,5.25-.189,7.428,1.437,2.608,1.948,5.811-.016,8.241,1.115,3.415,1.588,7.539,0,10.134,1.681,3.512,2.274,7.773-.79,10.614,2.541,3.846-.858,7.329,1.037,11.007,1.455,4.153.473,8.54.3,12.385,1.654,3.279,1.156,6.864.186,9.484,1.53,2.431,1.246,4.883.227,6.718,1.305,2.48,1.457,4.954,1.009,7.449,1.381,2.079.31,4.17.716,6.241,1.21,3.809.907,7.608,2.333,11.68,1.653.94-.156,2.461-.419,2.81.046,2.178,2.9,5.523.311,7.969,1.946,2.783,1.859,6.033.539,9.055.873.956.106,2.29-.39,2.837.089,2.05,1.8,4.756.6,6.634,1.544,2.589,1.3,4.585-.57,6.864-.187,3.412.572,6.846,1.025,10.279,1.464,2.931.375,5.875.658,9.17,1.022l.88-.749,4.372,2.251c3.027-1.5,5.668-1.624,8.294-.118,2.895,1.66,5.358-.224,7.877-1,2.9-.9,5.045,3.379,8.532.541,1.75-1.424,5.335-.592,8.4-.8a20.741,20.741,0,0,0-13.962-2.471c-4.736.734-9.12-2.716-13.81-.691-2.747-2.028-5.232-.547-7.808.725l-3.819-3.4-4.554,1.8c-2.119-1.482-4.334-2.521-6.569-.091l-2.77-2.77c-5.445-.271-11.126.608-16.611-1.428a27.189,27.189,0,0,0-7.36-1.462c-4.656-.349-9.265-.446-13.954-1.19-5-.8-10.022-2.788-15.327-1.864-.858.15-1.939-.98-2.973-1.548-.642.546-1.2,1.413-1.69,1.375q-5.837-.446-11.645-1.236c-.59-.079-1.085-.871-1.628-1.337-.489.446-1.009.919-1.8,1.634l-5.7-2.915c-.163.163-.311.447-.508.488-3.15.639-6.058-1.623-9.276-.8C440.221,286.711,439.558,285.76,439.642,285.832Z"}),e("path",{d:"M776.186,277.444c-.286.649-1.175,1.71-.927,2.251,2.609,5.7,1.018,11.344-.107,16.968-.927,4.632-1.644,9.486-4.4,13.387-5.11,7.228-8.268,16.132-16.9,20.468-.249,4.6-6.883,3.909-7.094,8.546-4.8,2.331-7.713,6.887-11.8,10.053-2.568,1.987-5.065,4.044-7.485,6.213-.931.833-2.482.973-4.178,1.582l-2.307,4.71-6.67,4.237c-.068,1.23-.157,2.857-.253,4.591l-2.513.917c-1.386,2.8,1.569,2.047,2.455,2.991-1.737,3.785-4.833,6.185-8.479,8.315l-7.268-5.553V367.4c-1-1.049-1.983-2.084-3.022-3.174.6-4.593-.837-9.306,1.381-13.94,1.066-2.227,1.379-4.865,3.407-6.537,1.3-1.07.8-3.617,3.464-3.75.585-.03,1-2.764,1.556-4.227,1.112-2.91,3.652-4.755,5.628-6.935A141.777,141.777,0,0,1,725.569,314c5.359-4.381,10.133-9.553,16.382-12.866,1.827-.968,3.185-2.8,4.814-4.172,1.576-1.326,3.223-2.568,5.281-4.2l-8.1-3.492-.959,2.6c-6.322-.586-11.856-4.281-18.4-5.529V283.1l-14.333-4.462c-1.946-.64-3.747,2.266-6.258.034l2.946-1.375c-3.476.638-6.458-5.281-10.157-.127-4.584-5.367-12.929-2.828-16.786-9.195L678.42,269.4c-3.633-1.378-7.3-3.273-11.189-4.1-4.042-.864-5.319-5.111-10.2-6.172a18.854,18.854,0,0,1-14.9-4.481c-.682-2.076-1.312-3.99-1.983-6.033l1.224-2.068-2.676-2.676,5.8-2.913-2.833-2.834,4.094-2.4c2.935,3.559,7.526.95,11.564,2.767,5.676,2.552,12.273,2.979,18.372,4.687,7.124,2,14.129,4.415,21.2,6.6,7.525,2.329,15.03,4.683,22.713,6.523,6.3,1.509,12.194,4.553,18.627,5.784,2.168,2.66,6.095.163,8.265,2.819,5.466-.56,9.95,3.341,15.308,3.377,1.07.008,2.113.76,3.207.958C769.9,270.123,774.49,271.391,776.186,277.444Z"})],-1),St=[bt,Ct],Ft={key:1,id:"workshop-exercises",class:"flex flex-col items-center justify-center bg-gray-800 rounded-lg shadow"},It={class:"px-4 py-5 sm:px-6 border-b w-full"},Ot={class:"text-lg leading-6 font-medium text-white"},Tt=e("p",{class:"mt-1 max-w-2xl text-sm text-gray-200"}," Pick an exercise to start hacking! ",-1),$t={id:"workshop-exercises-list",class:"flex flex-col w-full divide divide-y"},jt=["onClick"],Pt={class:"select-none cursor-pointer flex flex-1 items-center p-4"},Mt=e("div",{class:"flex flex-col w-10 h-10 justify-center items-center mr-4"},[e("a",{href:"#",class:"block relative"},[e("img",{alt:"workshop",src:q,class:"mx-auto object-cover h-10 w-10"})])],-1),Et={class:"flex-1 pl-1 mr-16 text-white group-hover:text-pink-600"},Lt={class:"font-medium"},Nt={class:"text-gray-300 text-sm"},Rt={class:"text-gray-200 text-xs bg-pink-600 py-1 px-3 rounded-full"},At={href:"#",class:"w-24 text-right flex justify-end"};function Vt(s,t,l,r,o,n){const f=a("ArrowRightIcon");return i(),c("div",ct,[e("div",dt,[e("div",at,[ut,e("ul",ft,[(i(!0),c(S,null,F(l.workshops,d=>(i(),c("li",{onClick:m=>n.selectWorkshop(d.code),class:b([{"bg-gray-700":o.selectedWorkshop&&d.code===o.selectedWorkshop.code},"group flex flex-row hover:bg-gray-600 last:rounded-b-lg"])},[e("div",mt,[pt,e("div",_t,[e("div",xt,_(d.name),1),e("div",gt,_(d.description),1)]),e("div",wt,_(d.type),1),e("a",yt,[h(f,{class:"group-hover:text-pink-600 text-gray-200 h-8 w-8"})])])],10,ht))),256))])])]),e("div",vt,[o.selectedWorkshop===null?(i(),c("div",kt,St)):u("",!0),o.selectedWorkshop?(i(),c("div",Ft,[e("div",It,[e("h3",Ot,_(o.selectedWorkshop.name)+" Exercises ",1),Tt]),e("ul",$t,[(i(!0),c(S,null,F(o.selectedWorkshop.exercises,d=>(i(),c("li",{onClick:m=>n.selectExercise(d),class:"group flex flex-row hover:bg-gray-600 last:hover:rounded-b-lg"},[e("div",Pt,[Mt,e("div",Et,[e("div",Lt,_(d.name),1),e("div",Nt,_(d.description),1)]),e("div",Rt,_(d.type),1),e("a",At,[h(f,{class:"group-hover:text-pink-600 text-gray-200 h-8 w-8"})])])],8,jt))),256))])])):u("",!0)])])}const Bt=y(rt,[["render",Vt]]),Wt={components:{Modal:M,FileTree:P,AceEditor:W,TrophyIcon:B,XMarkIcon:j},props:{nextExerciseLink:String,officialSolution:Array},data(){return{hasOfficialSolution:this.officialSolution!==null,currentSolutionFile:this.officialSolution!==null?this.officialSolution[0]:null,officialSolutionFileTree:this.officialSolution===null?[]:this.officialSolution.map((s,t)=>({id:t,name:s.file_path})),openOfficialSolutionModal:!1,fileTreeStyles:{selectedFileClasses:"bg-pink-500 rounded"}}},methods:{dismissPassNotification(){this.$emit("close")},showOfficialSolution(){this.openOfficialSolutionModal=!0},selectSolutionFile(s){this.currentSolutionFile=this.officialSolution.find(t=>t.file_path===s.name)},atob(s){return atob(s)},isSelectedFile(s){return this.currentSolutionFile&&s===de(this.currentSolutionFile)}}},zt={id:"pass-notification",class:"bg-green-500"},Xt={class:"mx-auto py-3 px-3 sm:px-6 lg:px-8"},Ht={class:"flex flex-wrap items-center justify-center"},qt={class:"flex items-center"},Dt={class:"flex rounded-lg bg-yellow-400 p-2"},Kt=e("p",{class:"ml-3 truncate font-medium text-white"},[e("span",{class:"md:hidden"},"Congratulations! your solution passed."),e("span",{class:"hidden md:inline"},"Congratulations! your solution passed.")],-1),Ut={key:0,class:"order-3 mt-2 w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto"},Jt={key:1,class:"order-3 mt-2 w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto flex"},Zt={key:0,class:"flex items-center justify-center text-sm text-white"},Gt=["href"],Yt={class:"order-2 flex-shrink-0 sm:order-3 sm:ml-3"},Qt=e("span",{class:"sr-only"},"Dismiss",-1),es=e("h3",{class:"text-base font-semibold lg:text-xl text-white"}," Official Solution ",-1),ts={class:"flex space-x-3"},ss={class:"w-1/3"},os={class:"w-2/3"};function ns(s,t,l,r,o,n){const f=a("TrophyIcon"),d=a("XMarkIcon"),m=a("file-tree"),p=a("AceEditor"),I=a("Modal");return i(),c("div",zt,[e("div",Xt,[e("div",Ht,[e("div",qt,[e("span",Dt,[h(f,{class:"h-6 w-6 text-white"})]),Kt]),o.hasOfficialSolution?(i(),c("div",Ut,[e("button",{onClick:t[0]||(t[0]=(...x)=>n.showOfficialSolution&&n.showOfficialSolution(...x)),class:"flex items-center justify-center px-2 py-2 text-sm font-bold text-white underline"}," See official solution ")])):u("",!0),l.nextExerciseLink?(i(),c("div",Jt,[o.hasOfficialSolution?(i(),c("span",Zt,"or")):u("",!0),e("a",{id:"next-exercise-link",href:l.nextExerciseLink,class:"flex items-center justify-center px-2 py-2 text-sm font-bold text-white underline"}," Continue to next exercise \u2192 ",8,Gt)])):u("",!0),e("div",Yt,[e("button",{onClick:t[1]||(t[1]=(...x)=>n.dismissPassNotification&&n.dismissPassNotification(...x)),type:"button",class:"-mr-1 flex rounded-md p-2 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2"},[Qt,h(d,{class:"h-6 w-6 text-white"})])])])]),o.openOfficialSolutionModal?(i(),v(I,{key:0,size:"3xl",onKeydown:t[2]||(t[2]=$(x=>o.openOfficialSolutionModal=!1,["esc"])),onClose:t[3]||(t[3]=x=>o.openOfficialSolutionModal=!1)},{header:C(()=>[es]),body:C(()=>[e("div",ts,[e("div",ss,[h(m,{files:o.officialSolutionFileTree,"file-select-function":n.selectSolutionFile,"initial-selected-item":o.officialSolutionFileTree[0],"custom-styles":o.fileTreeStyles},null,8,["files","file-select-function","initial-selected-item","custom-styles"])]),e("div",os,[(i(!0),c(S,null,F(l.officialSolution,x=>g((i(),v(p,{file_path:x.file_path,file_content:n.atob(x.content),readonly:""},null,8,["file_path","file_content"])),[[w,n.isSelectedFile(x)]])),256))])])]),_:1})):u("",!0)])}const D=y(Wt,[["render",ns]]),ls={components:{Modal:M,ArrowPathIcon:ae,ExclamationTriangleIcon:ue},emits:["verify-success"],props:{workshopCode:String,exerciseSlug:String},data(){return{loadingRun:!1,programOutput:"",openRunModal:!1,loadingVerify:!1,verifyResults:""}},methods:{runSolution(){this.loadingRun=!0;const s="/cloud/workshop/"+this.workshopCode+"/exercise/"+this.exerciseSlug+"/run",t=window.editor.getValue(),l={method:"POST",headers:{Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify({script:t})};fetch(s,l).then(r=>r.json()).then(r=>{this.programOutput=r.output,this.openRunModal=!0,this.loadingRun=!1})},verifySolution(){this.loadingVerify=!0,this.verifyResults="";const s="/cloud/workshop/"+this.workshopCode+"/exercise/"+this.exerciseSlug+"/verify",t=window.editor.getValue(),l={method:"POST",headers:{Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify({script:t})};fetch(s,l).then(r=>r.json()).then(r=>{this.verifyResults=r.results,r.success===!0&&this.$emit("verify-success"),this.loadingVerify=!1})}}},is={key:0},rs={key:0},cs=["innerHTML"],ds={class:"flex items-center"},as=e("h3",{class:"text-base font-semibold lg:text-xl text-white pt-0 mt-0"}," Program output ",-1),us={class:"mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1 overflow-x-auto"},fs={key:0,class:"mt-2 flex"},hs={class:"p-4 rounded text-sm font-mono bg-gray-800 text-white whitespace-pre-wrap flex-1 overflow-x-scroll"},ms={key:1,class:"",role:"alert"},ps=e("span",{class:"sr-only"},"Info",-1),_s=e("div",{class:"text-white"},"Your program produced no output.",-1),xs=[ps,_s],gs={class:"flex justify-end"};function ws(s,t,l,r,o,n){const f=a("ArrowPathIcon"),d=a("ExclamationTriangleIcon"),m=a("Modal");return i(),c(S,null,[e("button",{id:"run",class:"button mt-10 flex items-center justify-center",onClick:t[0]||(t[0]=(...p)=>n.runSolution&&n.runSolution(...p))},[g(h(f,{class:"w-4 h-4 animate-spin"},null,512),[[w,o.loadingRun]]),o.loadingRun?u("",!0):(i(),c("span",is,"Run"))]),e("button",{id:"verify",class:"button mt-2 flex items-center justify-center",onClick:t[1]||(t[1]=(...p)=>n.verifySolution&&n.verifySolution(...p))},[g(h(f,{class:"w-4 h-4 animate-spin"},null,512),[[w,o.loadingVerify]]),o.loadingVerify?u("",!0):(i(),c("span",rs,"Verify"))]),e("ul",{id:"results",class:"my-8 space-y-4 text-left text-gray-500 dark:text-gray-400",innerHTML:o.verifyResults},null,8,cs),o.openRunModal?(i(),v(m,{key:0,size:"xl",onKeydown:t[3]||(t[3]=$(p=>o.openRunModal=!1,["esc"])),onClose:t[4]||(t[4]=p=>o.openRunModal=!1)},{header:C(()=>[e("div",ds,[h(d,{class:"h-6 w-6 text-green-600 mr-2"}),as])]),body:C(()=>[e("div",us,[o.programOutput?(i(),c("div",fs,[e("p",hs,_(o.programOutput),1)])):u("",!0),o.programOutput?u("",!0):(i(),c("div",ms,xs))])]),footer:C(()=>[e("div",gs,[e("button",{type:"button",class:"inline-flex items-center w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm",onClick:t[2]||(t[2]=(...p)=>n.runSolution&&n.runSolution(...p))},[h(f,{class:b([{"animate-spin":o.loadingRun},"w-4 h-4 mr-2 -ml-1"])},null,8,["class"]),fe(" Run again ")])])]),_:1})):u("",!0)],64)}const K=y(ls,[["render",ws]]),ys={components:{PassNotification:D,FileTree:P,ExerciseVerify:K,Tabs:X,Tab:H},props:{nextExerciseLink:String,officialSolution:Array,workshopCode:String,exerciseSlug:String},data(){return{openPassNotification:!1,studentFiles:[{name:"solution.php"}]}},methods:{studentSelectFile(s){console.log(s)},dismissPassNotification(){this.openPassNotification=!1}}},vs={class:"flex p-4 space-x-2"},ks={class:"w-1/5"},bs={class:"w-3/5 flex flex-col justify-center"},Cs=e("pre",{id:"editor-problem",class:"h-screen w-full border-0"},"              ",-1),Ss=e("pre",{id:"editor",class:"h-screen w-full border-0"},"              ",-1),Fs={class:"w-1/5 flex flex-col ml-4"};function Is(s,t,l,r,o,n){const f=a("pass-notification"),d=a("file-tree"),m=a("Tab"),p=a("Tabs"),I=a("exercise-verify");return i(),c("div",null,[g(h(f,{"next-exercise-link":l.nextExerciseLink,"official-solution":l.officialSolution,onClose:n.dismissPassNotification},null,8,["next-exercise-link","official-solution","onClose"]),[[w,o.openPassNotification]]),e("div",vs,[e("div",ks,[h(d,{files:o.studentFiles,"file-select-function":n.studentSelectFile,"initial-selected-item":o.studentFiles[0],"show-controls":""},null,8,["files","file-select-function","initial-selected-item"])]),e("div",bs,[h(p,null,{default:C(()=>[h(m,{title:"Problem.md"},{default:C(()=>[Cs]),_:1}),h(m,{title:"solution.php"},{default:C(()=>[Ss]),_:1})]),_:1})]),e("div",Fs,[h(I,{onVerifySuccess:t[0]||(t[0]=x=>o.openPassNotification=!0),workshopCode:l.workshopCode,"exercise-slug":l.exerciseSlug},null,8,["workshopCode","exercise-slug"])])])])}const Os=y(ys,[["render",Is]]),Ts={AceEditor:W,FileTree:P,TreeItem:z,Tabs:X,Tab:H,Modal:M,StudentProgress:it,WorkshopExerciseSelectionList:Bt,PassNotification:D,ExerciseVerify:K,ExerciseEditor:Os},E=he({components:Ts});E.config.unwrapInjectedRef=!0;E.directive("click-outside",{mounted(s,t,l){s.clickOutsideEvent=function(r){s===r.target||s.contains(r.target)||t.value(r,s)},document.body.addEventListener("click",s.clickOutsideEvent)},unmounted(s){document.body.removeEventListener("click",s.clickOutsideEvent)}});E.mount("#app");
