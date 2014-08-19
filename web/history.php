<?php

include('utils/checkUser.php');
include('utils/model.php');

$user = getUser($seshUser);
$meals = getUserHistory($user['email']);
$weights = getWeightsForUser($user['email']);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=0.9">

        <title>History</title>

        <link rel="stylesheet" href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css"/>
        <script src="lib/jquery-2.1.1.min.js"></script>
        <script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>
        <script src="lib/d3.min.js"></script>

        <style>

        main
        {
            padding: 10px;
        }

        .glyphicon-beta
        {
            width: 14px;
            height: 14px;
            font-size: 14pt;
            text-align: center;
        }

        .line
        {
            stroke-width: 4px;
            fill: none;
        }

        .calorie-target
        {
            stroke: seagreen;

        }

        .meal-line
        {
            stroke: firebrick;
        }

        .weight-line
        {
            stroke: steelblue;
        }

        .axis path, .axis line
        {
            fill: none;
            stroke: black;
            shape-rendering: crispEdges;
        }

        .legend-label
        {
            fill: black;
            alignment-baseline: hanging;
        }

        #legend
        {
            margin-left: 50px;
            padding: 2px;

            border-style: solid;
            border-width: 2px;
            border-color: black;
            border-radius: 5px;
        }

        </style>

        <script>

        var calorieTarget = <?= $user['calorie_target']; ?>;

        var meals = <?= json_encode($meals); ?>;

        var weights = <?= json_encode($weights); ?>;

        $(document).ready(function ()
        {
            var width = 900;
            var height = 500;

            var margin = {top: 20, left: 60, bottom: 30, right: 20};

            var scaleX = d3.time.scale()
                            .domain([
                                d3.min(meals, function (m) { return new Date(m.date); }),
                                d3.max(meals, function (m) { return new Date(m.date); })
                            ])
                            .range([0, width]);

            var scaleY = d3.scale.linear()
                            .domain([0, Math.max(d3.max(meals, function (m) { return m.calories; }), calorieTarget)])
                            .range([height, 0]);

            var xAxis = d3.svg.axis()
                .scale(scaleX)
                .orient('bottom');

            var yAxis = d3.svg.axis()
                .scale(scaleY)
                .orient('left');

            var graph = d3.select('#graph')
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append('g')
                .attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');


            var calorieTargetLine = d3.svg.line()
                                        .x(function (d) { return d.x; })
                                        .y(function (d) { return scaleY(d.y); })
                                        .interpolate('linear');

            graph.append('path')
                .attr('class', 'line calorie-target')
                .attr('d', calorieTargetLine([{x: 0, y: calorieTarget}, {x: width, y: calorieTarget}]));

            var mealLine = d3.svg.line()
                .x(function (d) { return scaleX(new Date(d.date)); })
                .y(function (d) { return scaleY(d.calories); })
                .interpolate('linear');

            graph.append('path')
                .attr('class', 'line meal-line')
                .attr('d', mealLine(meals));

            graph.append('g')
                .attr('class', 'axis')
                .attr('transform', 'translate(0, ' + height + ')')
                .call(xAxis);

            graph.append('g')
                .attr('class', 'axis')
                .call(yAxis)
                .append('text')
                .attr('y', -15)
                .attr('x', -5)
                .attr('dy', '.71em')
                .style('text-anchor', 'end')
                .text('Calories');

            var weightScaleX = d3.time.scale()
                .domain([
                    d3.min(weights, function (w) { return new Date(w.date); }),
                    d3.max(weights, function (w) { return new Date(w.date); })
                ])
                .range([0, width]);

            var weightScaleY = d3.scale.linear()
                .domain([0, d3.max(weights, function (w) { return w.amount; })])
                .range([height, 0]);

            var weightLine = d3.svg.line()
                .x(function (d) { return weightScaleX(new Date(d.date)); })
                .y(function (d) { return weightScaleY(d.amount); })
                .interpolate('linear');

            graph.append('path')
                .attr('class', 'line weight-line')
                .attr('d', weightLine(weights));

            var weightAxis = d3.svg.axis()
                .scale(weightScaleY)
                .orient('right');

            graph.append('g')
                .attr('class', 'axis')
                .call(weightAxis)
                .append('text')
                .attr('y', -15)
                .attr('x', 85)
                .attr('dy', '.71em')
                .style('text-anchor', 'end')
                .text('Weight (lbs)');

        });

        </script>

    </head>
    <body>
        <header>
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#dt-navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand">Diet Tracker</a>
                    </div>

                    <div class="collapse navbar-collapse" id="dt-navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li><a href="index.php"><span class="glyphicon glyphicon-list"></span> Daily Report</a></li>
                            <li><a href="addMeal.php"><span class="glyphicon glyphicon-cutlery"></span> Add Meal</a></li>
                            <li><a href="addWeight.php"><span class="glyphicon glyphicon-inbox"></span> Record Weight</a></li>
                            <li><a href="addTarget.php"><span class="glyphicon glyphicon-screenshot"></span> Update Calorie Target</a></li>
                            <li><a href="history.php" class="active"><span class="glyphicon glyphicon-time"></span> History</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?=$user['name']?><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="userSettings.php"><span class="glyphicon glyphicon-cog"></span> Account Settings</a></li>
                                    <li><a href="https://github.com/Queuecumber/diet-tracker/issues"><span class="glyphicon glyphicon-beta">&beta;</span> Report Issue</a></li>
                                    <li class="divider"></li>
                                    <li><a href="logOff.php"><span class="glyphicon glyphicon-off"></span> Log Off</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <h1>History <small>Calorie Target, Daily Calories, and Weight vs Time</small></h1>
            <div id="graph-container">
                <svg id="graph"></svg>
                <br/>
                <svg id="legend" width="150" height="90">
                    <line class="calorie-target line" x1="0" y1="5" x2="200" y2="5"/>
                    <text class="legend-label" x="0" y="9">Calorie Target</text>
                    <line class="meal-line line" x1="0" y1="35" x2="200" y2="35"/>
                    <text class="legend-label" x="0" y="39">Daily Calories</text>
                    <line class="weight-line line" x1="0" y1="65" x2="200" y2="65"/>
                    <text class="legend-label" x="0" y="69">Weight</text>
                </svg>
            </div>
        </main>
    </body>
</html>
