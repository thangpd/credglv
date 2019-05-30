jQuery(function ($) {
    "use strict";
    var credglv = window.credglv || {};

    credglv.credglv_d3 = function (data) {

        //build tree
        var margin = {top: 20, right: 120, bottom: 20, left: 120},
            width = $('#collapsable-example').width() - margin.right - margin.left,
            height = $('#collapsable-example').height() - margin.top - margin.bottom;

        var i = 0,
            duration = 750,
            root;

        var tree = d3.layout.tree()
            .size([height, width]);

        var diagonal = d3.svg.diagonal()
            .projection(function (d) {
                return [d.y, d.x];
            });

        var svg = d3.select("#collapsable-example").append("svg")
            .attr("width", width + margin.right + margin.left)
            .attr("height", height + margin.top + margin.bottom).call(d3.behavior.zoom().on("zoom", function () {
                svg.attr("transform", "translate(" + d3.event.translate + ")" + " scale(" + d3.event.scale + ")")
            }))
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


        root = data;
        root.x0 = height / 2;
        root.y0 = 0;

        function collapse(d) {
            if (d.children) {
                d._children = d.children;
                d._children.forEach(collapse);
                d.children = null;
            }
        }

        root.children.forEach(collapse);
        update(root);

        function update(source) {

            // Compute the new tree layout.
            var nodes = tree.nodes(root).reverse(),
                links = tree.links(nodes);

            // Normalize for fixed-depth.
            nodes.forEach(function (d) {
                d.y = d.depth * 180;
            });

            // Update the nodes…
            var node = svg.selectAll("g.node")
                .data(nodes, function (d) {
                    return d.id || (d.id = ++i);
                });

            // Enter any new nodes at the parent's previous position.
            var nodeEnter = node.enter().append("g")
                .attr("class", "node")
                .attr("transform", function (d) {
                    return "translate(" + source.y0 + "," + source.x0 + ")";
                })
            ;

            // add picture
            nodeEnter
                .append('defs')
                .append('pattern')
                .attr('id', function (d, i) {
                    return 'pic_' + d.display_name;
                })
                .attr('height', 60)
                .attr('width', 60)
                .attr('x', 0)
                .attr('y', 0)
                .append('image')
                .attr('xlink:href', function (d, i) {
                    return d.photo;
                })
                .attr('height', 60)
                .attr('width', 60)
                .attr('x', 0)
                .attr('y', 0);

            nodeEnter.append("circle")
                .attr("r", 1e-6)
                .style("fill", function (d) {
                    return d.children ? "lightsteelblue" : "#fff";
                }).on("click", click);

            var g = nodeEnter.append("g");


            g.append("text")
                .attr("y", function (d) {
                    return d.children || d._children ? 35 : 35;
                })
                .attr("dy", "1.35em")
                .attr("text-anchor", function (d) {
                    return d.children || d._children ? "middle" : "middle";
                })
                .text(function (d) {
                    var d_display_name = d.display_name;
                    return d_display_name.toString().toUpperCase();
                })
                .style("fill-opacity", 1e-6).on("click", username_click);

            g.append("text")
                .attr("x", function (d) {
                    return d.children || d._children ? -35 : 35;
                })
                .attr("dy", "2.5em")
                .attr("text-anchor", function (d) {
                    return d.children || d._children ? "end" : "start";
                })
                .text(function (d) {
                    return d.title;
                })
                .style("fill-opacity", 1e-6);

            g.append("text")
                .attr("y", function (d) {
                    return d.children || d._children ? 50 : 50;
                })
                .attr("dy", "1.35em")
                .attr("text-anchor", function (d) {
                    return d.children || d._children ? "middle" : "middle";
                })
                .text(function (d) {
                    var d_display_fullname = d.display_fullname;
                    return d_display_fullname.toString().toUpperCase();
                })
                .style("fill-opacity", 1e-6).on("click", username_click);

            g.append("text")
                .attr("x", function (d) {
                    return d.children || d._children ? -50 : 50;
                })
                .attr("dy", "2.5em")
                .attr("text-anchor", function (d) {
                    return d.children || d._children ? "end" : "start";
                })
                .text(function (d) {
                    return d.title;
                })
                .style("fill-opacity", 1e-6);

            // Transition nodes to their new position.
            var nodeUpdate = node.transition()
                .duration(duration)
                .attr("transform", function (d) {
                    return "translate(" + d.y + "," + d.x + ")";
                });

            nodeUpdate.select("circle")
                .attr("r", 30)
                .style("fill", function (d, i) {
                    return 'url(#pic_' + d.display_name + ')';
                }).style("stroke-width", function (d) {
                return d.children || d._children ? 5 : 1;
            });

            nodeUpdate.selectAll("text")
                .style("fill-opacity", 1);

            // Transition exiting nodes to the parent's new position.
            var nodeExit = node.exit().transition()
                .duration(duration)
                .attr("transform", function (d) {
                    return "translate(" + source.y + "," + source.x + ")";
                })
                .remove();

            nodeExit.select("circle")
                .attr("r", 1e-6);

            nodeExit.select("text")
                .style("fill-opacity", 1e-6);

            // Update the links…
            var link = svg.selectAll("path.link")
                .data(links, function (d) {
                    return d.target.id;
                });

            // Enter any new links at the parent's previous position.
            link.enter().insert("path", "g")
                .attr("class", "link")
                .attr("d", function (d) {
                    var o = {x: source.x0, y: source.y0};
                    return diagonal({source: o, target: o});
                });

            // Transition links to their new position.
            link.transition()
                .duration(duration)
                .attr("d", diagonal);

            // Transition exiting nodes to the parent's new position.
            link.exit().transition()
                .duration(duration)
                .attr("d", function (d) {
                    var o = {x: source.x, y: source.y};
                    return diagonal({source: o, target: o});
                })
                .remove();

            // Stash the old positions for transition.
            nodes.forEach(function (d) {
                d.x0 = d.x;
                d.y0 = d.y;
            });
        }

// Toggle children on click.
        function click(d) {
            if (d.children) {
                d._children = d.children;
                d.children = null;
            } else {
                d.children = d._children;
                d._children = null;
            }
            update(d);
        }

        function username_click(d) {
            if(d.level != 0){
                $('form#mycred-transfer-form-transfer').animate({
                    scrollTop: $("#transfer-form-transfer").offset().top
                }, 2000);
                console.log(d);
                var input_username = $('input[name="mycred_new_transfer[recipient_id]"]');
                input_username.val(d.display_name);
                $('input[name="mycred_new_transfer[amount]"]').focus();
                console.log(input_username);
            }
        }
    }


    $(document).ready(function () {

        /*var data = {
            "display_name": "Rogers",
            "title": "CEO",
            "photo": "http://lorempixel.com/60/60/cats/1",
            "children": [{
                "display_name": "Smith",
                "title": "President",
                "photo": "http://lorempixel.com/60/60/cats/2",
                "children": [{
                    "display_name": "Jane",
                    "title": "Vice President",
                    "photo": "http://lorempixel.com/60/60/cats/3",
                    "children": [{
                        "display_name": "August",
                        "title": "Dock Worker",
                        "photo": "http://lorempixel.com/60/60/cats/4"
                    }, {
                        "display_name": "Yoyo",
                        "title": "Line Assembly",
                        "photo": "http://lorempixel.com/60/60/cats/5"
                    }]
                }, {
                    "display_name": "Ringwald",
                    "title": "Comptroller",
                    "photo": "http://lorempixel.com/60/60/cats/6"
                }]
            }]
        };*/
        var data = $('#collapsable-example').data('data');
        credglv.credglv_d3(data);

    });
});