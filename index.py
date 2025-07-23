#!/usr/bin/env python3
"""
House-Tree-Person Test Advanced Data Analysis
Python backend for psychological pattern recognition and machine learning analysis
"""

import json
import numpy as np
import pandas as pd
from datetime import datetime
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
import matplotlib.pyplot as plt
import seaborn as sns
from flask import Flask, request, jsonify
import logging

class HTPDataAnalyzer:
    """Advanced psychological analysis using machine learning techniques"""
    
    def __init__(self):
        self.phases = {
            1: {'name': 'House', 'psychological_domain': 'Security & Family'},
            2: {'name': 'Tree', 'psychological_domain': 'Growth & Stability'}, 
            3: {'name': 'Person', 'psychological_domain': 'Self-Image & Relations'}
        }
        
        # Psychological scoring weights
        self.scoring_weights = {
            'time_investment': 0.25,
            'complexity_score': 0.30,
            'color_diversity': 0.20,
            'consistency_factor': 0.25
        }
        
        logging.basicConfig(level=logging.INFO)
        self.logger = logging.getLogger(__name__)
    
    def analyze_drawing_patterns(self, drawing_data):
        """
        Main analysis function using advanced pattern recognition
        """
        try:
            # Validate input data
            if not self.validate_input(drawing_data):
                return {'error': 'Invalid input data format'}
            
            # Extract features for ML analysis
            features = self.extract_psychological_features(drawing_data)
            
            # Perform clustering analysis
            personality_cluster = self.perform_personality_clustering(features)
            
            # Generate comprehensive analysis
            analysis = {
                'timestamp': datetime.now().isoformat(),
                'feature_analysis': features,
                'personality_cluster': personality_cluster,
                'psychological_profile': self.generate_psychological_profile(features),
                'behavioral_insights': self.analyze_behavioral_patterns(drawing_data),
                'recommendations': self.generate_personalized_recommendations(features),
                'statistical_summary': self.generate_statistical_summary(drawing_data)
            }
            
            self.logger.info(f"Analysis completed for {len(drawing_data['phases'])} phases")
            return analysis
            
        except Exception as e:
            self.logger.error(f"Analysis error: {str(e)}")
            return {'error': f'Analysis failed: {str(e)}'}
    
    def validate_input(self, data):
        """Validate input data structure"""
        required_fields = ['phases']
        if not isinstance(data, dict) or 'phases' not in data:
            return False
        
        for phase in data['phases']:
            required_phase_fields = ['phase', 'timeSpent', 'strokeCount', 'colorsUsed']
            if not all(field in phase for field in required_phase_fields):
                return False
        
        return True
    
    def extract_psychological_features(self, drawing_data):
        """Extract numerical features for ML analysis"""
        phases = drawing_data['phases']
        
        # Time-based features
        time_features = self.extract_time_features(phases)
        
        # Complexity features
        complexity_features = self.extract_complexity_features(phases)
        
        # Color usage features
        color_features = self.extract_color_features(phases)
        
        # Consistency features
        consistency_features = self.extract_consistency_features(phases)
        
        return {
            'time_features': time_features,
            'complexity_features': complexity_features,
            'color_features': color_features,
            'consistency_features': consistency_features,
            'composite_scores': self.calculate_composite_scores(
                time_features, complexity_features, color_features, consistency_features
            )
        }
    
    def extract_time_features(self, phases):
        """Extract time-based psychological indicators"""
        times = [phase['timeSpent'] / 60000 for phase in phases]  # Convert to minutes
        
        return {
            'total_time': sum(times),
            'average_time': np.mean(times),
            'time_variance': np.var(times),
            'time_distribution': times,
            'phase_priorities': self.calculate_phase_priorities(times),
            'time_efficiency_score': self.calculate_time_efficiency(times, phases)
        }
    
    def extract_complexity_features(self, phases):
        """Extract complexity and detail-oriented indicators"""
        strokes = [phase['strokeCount'] for phase in phases]
        coverage = [phase.get('coverage', 0) for phase in phases]
        
        return {
            'total_strokes': sum(strokes),
            'average_strokes': np.mean(strokes),
            'stroke_variance': np.var(strokes),
            'detail_progression': self.calculate_detail_progression(strokes),
            'complexity_score': self.calculate_complexity_score(strokes, coverage),
            'artistic_intensity': self.calculate_artistic_intensity(phases)
        }
    
    def extract_color_features(self, phases):
        """Extract color usage patterns for emotional analysis"""
        all_colors = []
        color_counts = []
        
        for phase in phases:
            colors = phase['colorsUsed']
            all_colors.extend(colors)
            color_counts.append(len(colors))
        
        unique_colors = len(set(all_colors))
        
        return {
            'total_unique_colors': unique_colors,
            'color_per_phase': color_counts,
            'color_diversity_score': self.calculate_color_diversity(all_colors),
            'emotional_expression_level': self.assess_emotional_expression(color_counts),
            'color_consistency': self.calculate_color_consistency(phases)
        }
    
    def extract_consistency_features(self, phases):
        """Extract behavioral consistency indicators"""
        times = [phase['timeSpent'] for phase in phases]
        strokes = [phase['strokeCount'] for phase in phases]
        colors = [len(phase['colorsUsed']) for phase in phases]
        
        return {
            'time_consistency': 1 - (np.std(times) / np.mean(times) if np.mean(times) > 0 else 0),
            'stroke_consistency': 1 - (np.std(strokes) / np.mean(strokes) if np.mean(strokes) > 0 else 0),
            'color_consistency': 1 - (np.std(colors) / np.mean(colors) if np.mean(colors) > 0 else 0),
            'overall_consistency': self.calculate_overall_consistency(times, strokes, colors)
        }
    
    def calculate_phase_priorities(self, times):
        """Determine psychological priorities based on time investment"""
        phase_names = ['Home & Security', 'Personal Growth', 'Self-Image']
        priorities = sorted(zip(phase_names, times), key=lambda x: x[1], reverse=True)
        return [{'domain': name, 'time_minutes': time, 'priority_rank': i+1} 
                for i, (name, time) in enumerate(priorities)]
    
    def calculate_complexity_score(self, strokes, coverage):
        """Calculate overall complexity score"""
        stroke_score = np.mean(strokes) / 100  # Normalize
        coverage_score = np.mean(coverage) / 100
        return min((stroke_score + coverage_score) / 2, 1.0)
    
    def calculate_color_diversity(self, all_colors):
        """Calculate color diversity using entropy-like measure"""
        from collections import Counter
        color_counts = Counter(all_colors)
        total = len(all_colors)
        
        if total <= 1:
            return 0
        
        entropy = -sum((count/total) * np.log2(count/total) for count in color_counts.values())
        max_entropy = np.log2(len(color_counts))
        
        return entropy / max_entropy if max_entropy > 0 else 0
    
    def perform_personality_clustering(self, features):
        """Perform K-means clustering to identify personality types"""
        try:
            # Prepare feature vector for clustering
            composite = features['composite_scores']
            feature_vector = np.array([
                composite['psychological_investment'],
                composite['creative_expression'],
                composite['behavioral_consistency'],
                composite['attention_to_detail']
            ]).reshape(1, -1)
            
            # Define personality clusters (predefined centroids)
            personality_clusters = {
                0: {'type': 'Analytical', 'description': 'Detail-oriented, systematic, consistent'},
                1: {'type': 'Creative', 'description': 'Expressive, colorful, varied approach'},
                2: {'type': 'Efficient', 'description': 'Quick, decisive, minimalist'},
                3: {'type': 'Thoughtful', 'description': 'Reflective, thorough, balanced'}
            }
            
            # Simple clustering based on feature thresholds
            cluster = self.determine_personality_cluster(composite)
            
            return {
                'cluster_id': cluster,
                'personality_type': personality_clusters[cluster]['type'],
                'description': personality_clusters[cluster]['description'],
                'confidence_score': self.calculate_cluster_confidence(feature_vector, cluster)
            }
            
        except Exception as e:
            self.logger.error(f"Clustering error: {str(e)}")
            return {'cluster_id': 0, 'personality_type': 'Unknown', 'description': 'Analysis incomplete'}
    
    def determine_personality_cluster(self, composite):
        """Determine personality cluster based on composite scores"""
        scores = composite
        
        # Rule-based clustering
        if scores['attention_to_detail'] > 0.7 and scores['behavioral_consistency'] > 0.6:
            return 0  # Analytical
        elif scores['creative_expression'] > 0.6 and scores['psychological_investment'] > 0.5:
            return 1  # Creative
        elif scores['psychological_investment'] < 0.4 and scores['behavioral_consistency'] > 0.5:
            return 2  # Efficient
        else:
            return 3  # Thoughtful
    
    def calculate_composite_scores(self, time_f, complexity_f, color_f, consistency_f):
        """Calculate weighted composite psychological scores"""
        return {
            'psychological_investment': min((
                time_f['total_time'] / 15 * 0.4 +  # Normalize to 15 minutes max
                complexity_f['complexity_score'] * 0.6
            ), 1.0),
            
            'creative_expression': min((
                color_f['color_diversity_score'] * 0.5 +
                color_f['emotional_expression_level'] * 0.5
            ), 1.0),
            
            'behavioral_consistency': (
                consistency_f['overall_consistency']
            ),
            
            'attention_to_detail': min((
                complexity_f['total_strokes'] / 300 * 0.6 +  # Normalize to 300 strokes max
                complexity_f['artistic_intensity'] * 0.4
            ), 1.0)
        }
    
    def generate_psychological_profile(self, features):
        """Generate comprehensive psychological profile"""
        composite = features['composite_scores']
        
        profile = {
            'cognitive_style': self.assess_cognitive_style(composite),
            'emotional_expression': self.assess_emotional_expression_style(features['color_features']),
            'behavioral_patterns': self.assess_behavioral_patterns(features['consistency_features']),
            'psychological_priorities': features['time_features']['phase_priorities'],
            'overall_assessment': self.generate_overall_assessment(composite)
        }
        
        return profile
    
    def assess_cognitive_style(self, composite):
        """Assess cognitive processing style"""
        detail_score = composite['attention_to_detail']
        consistency_score = composite['behavioral_consistency']
        
        if detail_score > 0.7:
            if consistency_score > 0.6:
                return {'style': 'Systematic Processor', 'description': 'Methodical, thorough, detail-oriented'}
            else:
                return {'style': 'Complex Processor', 'description': 'Detailed but flexible approach'}
        elif detail_score < 0.3:
            return {'style': 'Global Processor', 'description': 'Big-picture focus, efficient processing'}
        else:
            return {'style': 'Balanced Processor', 'description': 'Adaptive between detail and overview'}
    
    def generate_statistical_summary(self, drawing_data):
        """Generate statistical summary of drawing behavior"""
        phases = drawing_data['phases']
        
        times = [p['timeSpent'] / 60000 for p in phases]
        strokes = [p['strokeCount'] for p in phases]
        colors = [len(p['colorsUsed']) for p in phases]
        
        return {
            'descriptive_statistics': {
                'time_stats': {
                    'mean': round(np.mean(times), 2),
                    'std': round(np.std(times), 2),
                    'min': round(min(times), 2),
                    'max': round(max(times), 2)
                },
                'stroke_stats': {
                    'mean': round(np.mean(strokes), 2),
                    'std': round(np.std(strokes), 2),
                    'min': min(strokes),
                    'max': max(strokes)
                },
                'color_stats': {
                    'mean': round(np.mean(colors), 2),
                    'std': round(np.std(colors), 2),
                    'min': min(colors),
                    'max': max(colors)
                }
            },
            'correlations': self.calculate_feature_correlations(times, strokes, colors),
            'percentile_rankings': self.calculate_percentile_rankings(times, strokes, colors)
        }
    
    def calculate_feature_correlations(self, times, strokes, colors):
        """Calculate correlations between different drawing features"""
        data = np.array([times, strokes, colors])
        correlation_matrix = np.corrcoef(data)
        
        return {
            'time_stroke_correlation': round(correlation_matrix[0, 1], 3),
            'time_color_correlation': round(correlation_matrix[0, 2], 3),
            'stroke_color_correlation': round(correlation_matrix[1, 2], 3)
        }
    
    # Additional helper methods would continue here...
    def calculate_time_efficiency(self, times, phases):
        """Calculate time efficiency score"""
        return sum(times) / sum(p['strokeCount'] for p in phases) if sum(p['strokeCount'] for p in phases) > 0 else 0
    
    def calculate_detail_progression(self, strokes):
        """Calculate how detail level changes across phases"""
        if len(strokes) < 2:
            return 0
        differences = [strokes[i+1] - strokes[i] for i in range(len(strokes)-1)]
        return np.mean(differences)
    
    def calculate_artistic_intensity(self, phases):
        """Calculate overall artistic intensity"""
        total_activity = sum(p['strokeCount'] + len(p['colorsUsed']) for p in phases)
        total_time = sum(p['timeSpent'] for p in phases) / 60000  # Convert to minutes
        return total_activity / total_time if total_time > 0 else 0
    
    def assess_emotional_expression(self, color_counts):
        """Assess emotional expression level based on color usage"""
        avg_colors = np.mean(color_counts)
        if avg_colors > 3:
            return 'High'
        elif avg_colors > 1.5:
            return 'Moderate' 
        else:
            return 'Low'
    
    def calculate_color_consistency(self, phases):
        """Calculate consistency in color usage across phases"""
        color_counts = [len(p['colorsUsed']) for p in phases]
        return 1 - (np.std(color_counts) / np.mean(color_counts)) if np.mean(color_counts) > 0 else 1
    
    def calculate_overall_consistency(self, times, strokes, colors):
        """Calculate overall behavioral consistency"""
        time_cv = np.std(times) / np.mean(times) if np.mean(times) > 0 else 0
        stroke_cv = np.std(strokes) / np.mean(strokes) if np.mean(strokes) > 0 else 0
        color_cv = np.std(colors) / np.mean(colors) if
